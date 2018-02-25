/*!
 * jQVMap Version 1.0 
 *
 * http://jqvmap.com
 *
 * Copyright 2012, Peter Schmalfeldt <manifestinteractive@gmail.com>
 * Copyright 2011-2012, Kirill Lebedev
 * Licensed under the MIT license.
 *
 * Fork Me @ https://github.com/manifestinteractive/jqvmap
 */
(function ($) {

    var apiParams = {
        colors: 1,
        values: 1,
        backgroundColor: 1,
        scaleColors: 1,
        normalizeFunction: 1,
        enableZoom: 1,
        showTooltip: 1,
        borderColor: 1,
        borderWidth: 1,
        borderOpacity: 1,
        selectedRegion: 1
    };

    var apiEvents = {
        onLabelShow: 'labelShow',
        onRegionOver: 'regionMouseOver',
        onRegionOut: 'regionMouseOut',
        onRegionClick: 'regionClick'
    };

    $.fn.vectorMap = function (options) {

        var defaultParams = {
            map: 'world_en',
            backgroundColor: '#a5bfdd',
            color: '#f4f3f0',
            hoverColor: '#c9dfaf',
            selectedColor: '#c9dfaf',
            scaleColors: ['#b6d6ff', '#005ace'],
            normalizeFunction: 'linear',
            enableZoom: true,
            showTooltip: true,
            borderColor: '#818181',
            borderWidth: 1,
            borderOpacity: 0.25,
            selectedRegion: null
        }, map;

        if (options === 'addMap') {
            WorldMap.maps[arguments[1]] = arguments[2];
        }
        else if (options === 'set' && apiParams[arguments[1]]) {
            this.data('mapObject')['set' + arguments[1].charAt(0).toUpperCase() + arguments[1].substr(1)].apply(this.data('mapObject'), Array.prototype.slice.call(arguments, 2));
        }
        else {
            $.extend(defaultParams, options);
            defaultParams.container = this;
            this.css({position: 'relative', overflow: 'hidden'});

            map = new WorldMap(defaultParams);

            this.data('mapObject', map);

            for (var e in apiEvents) {
                if (defaultParams[e]) {
                    this.bind(apiEvents[e] + '.jqvmap', defaultParams[e]);
                }
            }
        }
    };

    var VectorCanvas = function (width, height, params) {
        this.mode = window.SVGAngle ? 'svg' : 'vml';
        this.params = params;

        if (this.mode == 'svg') {
            this.createSvgNode = function (nodeName) {
                return document.createElementNS(this.svgns, nodeName);
            };
        }
        else {
            try {
                if (!document.namespaces.rvml) {
                    document.namespaces.add("rvml", "urn:schemas-microsoft-com:vml");
                }
                this.createVmlNode = function (tagName) {
                    return document.createElement('<rvml:' + tagName + ' class="rvml">');
                };
            }
            catch (e) {
                this.createVmlNode = function (tagName) {
                    return document.createElement('<' + tagName + ' xmlns="urn:schemas-microsoft.com:vml" class="rvml">');
                };
            }

            document.createStyleSheet().addRule(".rvml", "behavior:url(#default#VML)");
        }

        if (this.mode == 'svg') {
            this.canvas = this.createSvgNode('svg');
        }
        else {
            this.canvas = this.createVmlNode('group');
            this.canvas.style.position = 'absolute';
        }

        this.setSize(width, height);
    };

    VectorCanvas.prototype = {
        svgns: "http://www.w3.org/2000/svg",
        mode: 'svg',
        width: 0,
        height: 0,
        canvas: null,

        setSize: function (width, height) {
            if (this.mode == 'svg') {
                this.canvas.setAttribute('width', width);
                this.canvas.setAttribute('height', height);
            }
            else {
                this.canvas.style.width = width + "px";
                this.canvas.style.height = height + "px";
                this.canvas.coordsize = width + ' ' + height;
                this.canvas.coordorigin = "0 0";
                if (this.rootGroup) {
                    var pathes = this.rootGroup.getElementsByTagName('shape');
                    for (var i = 0, l = pathes.length; i < l; i++) {
                        pathes[i].coordsize = width + ' ' + height;
                        pathes[i].style.width = width + 'px';
                        pathes[i].style.height = height + 'px';
                    }
                    this.rootGroup.coordsize = width + ' ' + height;
                    this.rootGroup.style.width = width + 'px';
                    this.rootGroup.style.height = height + 'px';
                }
            }
            this.width = width;
            this.height = height;
        },

        createPath: function (config) {
            var node;
            if (this.mode == 'svg') {
                node = this.createSvgNode('path');
                node.setAttribute('d', config.path);

                if (this.params.borderColor !== null) {
                    node.setAttribute('stroke', this.params.borderColor);
                }
                if (this.params.borderWidth > 0) {
                    node.setAttribute('stroke-width', this.params.borderWidth);
                    node.setAttribute('stroke-linecap', 'round');
                    node.setAttribute('stroke-linejoin', 'round');
                }
                if (this.params.borderOpacity > 0) {
                    node.setAttribute('stroke-opacity', this.params.borderOpacity);
                }

                node.setFill = function (color) {
                    this.setAttribute("fill", color);
                    if (this.getAttribute("original") === null) {
                        this.setAttribute("original", color);
                    }
                };

                node.getFill = function (color) {
                    return this.getAttribute("fill");
                };

                node.getOriginalFill = function () {
                    return this.getAttribute("original");
                };

                node.setOpacity = function (opacity) {
                    this.setAttribute('fill-opacity', opacity);
                };
            }
            else {
                node = this.createVmlNode('shape');
                node.coordorigin = "0 0";
                node.coordsize = this.width + ' ' + this.height;
                node.style.width = this.width + 'px';
                node.style.height = this.height + 'px';
                node.fillcolor = WorldMap.defaultFillColor;
                node.stroked = false;
                node.path = VectorCanvas.pathSvgToVml(config.path);

                var scale = this.createVmlNode('skew');
                scale.on = true;
                scale.matrix = '0.01,0,0,0.01,0,0';
                scale.offset = '0,0';

                node.appendChild(scale);

                var fill = this.createVmlNode('fill');
                node.appendChild(fill);

                node.setFill = function (color) {
                    this.getElementsByTagName('fill')[0].color = color;
                };

                node.getFill = function (color) {
                    return this.getElementsByTagName('fill')[0].color;
                };

                node.setOpacity = function (opacity) {
                    this.getElementsByTagName('fill')[0].opacity = parseInt(opacity * 100, 10) + '%';
                };
            }
            return node;
        },

        createGroup: function (isRoot) {
            var node;
            if (this.mode == 'svg') {
                node = this.createSvgNode('g');
            }
            else {
                node = this.createVmlNode('group');
                node.style.width = this.width + 'px';
                node.style.height = this.height + 'px';
                node.style.left = '0px';
                node.style.top = '0px';
                node.coordorigin = "0 0";
                node.coordsize = this.width + ' ' + this.height;
            }

            if (isRoot) {
                this.rootGroup = node;
            }
            return node;
        },

        applyTransformParams: function (scale, transX, transY) {
            transX = (transX === Infinity) ? 0 : transX;
            transY = (transX === Infinity) ? 0 : transY;
            scale = (scale === Infinity) ? 0 : scale;

            var scale = parseFloat(scale) || 0;
            var transX = parseFloat(transX) || 0;
            var transY = parseFloat(transY) || 0;
            if (this.mode == 'svg') {
                this.rootGroup.setAttribute('transform', 'scale('+scale+') translate(' + transX + ', ' + transY + ')');
            }
            else {
                this.rootGroup.coordorigin = (this.width - transX) + ',' + (this.height - transY);
                this.rootGroup.coordsize = this.width / scale + ',' + this.height / scale;
            }
        }
    };

    VectorCanvas.pathSvgToVml = function (path) {
        var result = '';
        var cx = 0, cy = 0, ctrlx, ctrly;

        return path.replace(/([MmLlHhVvCcSs])((?:-?(?:\d+)?(?:\.\d+)?,?\s?)+)/g, function (segment, letter, coords, index) {
            coords = coords.replace(/(\d)-/g, '$1,-').replace(/\s+/g, ',').split(',');
            if (!coords[0]) {
                coords.shift();
            }

            for (var i = 0, l = coords.length; i < l; i++) {
                coords[i] = Math.round(100 * coords[i]);
            }

            switch (letter) {
                case 'm':
                    cx += coords[0];
                    cy += coords[1];
                    return 't' + coords.join(',');
                    break;

                case 'M':
                    cx = coords[0];
                    cy = coords[1];
                    return 'm' + coords.join(',');
                    break;

                case 'l':
                    cx += coords[0];
                    cy += coords[1];
                    return 'r' + coords.join(',');
                    break;

                case 'L':
                    cx = coords[0];
                    cy = coords[1];
                    return 'l' + coords.join(',');
                    break;

                case 'h':
                    cx += coords[0];
                    return 'r' + coords[0] + ',0';
                    break;

                case 'H':
                    cx = coords[0];
                    return 'l' + cx + ',' + cy;
                    break;

                case 'v':
                    cy += coords[0];
                    return 'r0,' + coords[0];
                    break;

                case 'V':
                    cy = coords[0];
                    return 'l' + cx + ',' + cy;
                    break;

                case 'c':
                    ctrlx = cx + coords[coords.length - 4];
                    ctrly = cy + coords[coords.length - 3];
                    cx += coords[coords.length - 2];
                    cy += coords[coords.length - 1];
                    return 'v' + coords.join(',');
                    break;

                case 'C':
                    ctrlx = coords[coords.length - 4];
                    ctrly = coords[coords.length - 3];
                    cx = coords[coords.length - 2];
                    cy = coords[coords.length - 1];
                    return 'c' + coords.join(',');
                    break;

                case 's':
                    coords.unshift(cy - ctrly);
                    coords.unshift(cx - ctrlx);
                    ctrlx = cx + coords[coords.length - 4];
                    ctrly = cy + coords[coords.length - 3];
                    cx += coords[coords.length - 2];
                    cy += coords[coords.length - 1];
                    return 'v' + coords.join(',');
                    break;

                case 'S':
                    coords.unshift(cy + cy - ctrly);
                    coords.unshift(cx + cx - ctrlx);
                    ctrlx = coords[coords.length - 4];
                    ctrly = coords[coords.length - 3];
                    cx = coords[coords.length - 2];
                    cy = coords[coords.length - 1];
                    return 'c' + coords.join(',');
                    break;

                default:
                    return false;
                    break;
            }

            return '';

        }).replace(/z/g, '');
    };

    var WorldMap = function (params) {
        params = params || {};
        var map = this;
        var mapData = WorldMap.maps[params.map];

        this.container = params.container;

        this.defaultWidth = mapData.width;
        this.defaultHeight = mapData.height;

        this.color = params.color;
        this.hoverColor = params.hoverColor;
        this.setBackgroundColor(params.backgroundColor);

        this.width = params.container.width();
        this.height = params.container.height();

        this.resize();

        jQuery(window).resize(function () {
            map.width = params.container.width();
            map.height = params.container.height();
            map.resize();
            map.canvas.setSize(map.width, map.height);
            map.applyTransform();
        });

        this.canvas = new VectorCanvas(this.width, this.height, params);
        params.container.append(this.canvas.canvas);

        this.makeDraggable();

        this.rootGroup = this.canvas.createGroup(true);

        this.index = WorldMap.mapIndex;
        this.label = jQuery('<div/>').addClass('jqvmap-label').appendTo(jQuery('body'));

        if (params.enableZoom) {
            jQuery('<div/>').addClass('jqvmap-zoomin').text('+').appendTo(params.container);
            jQuery('<div/>').addClass('jqvmap-zoomout').html('&#x2212;').appendTo(params.container);
        }

        map.countries = [];

        for (var key in mapData.pathes) {
            var path = this.canvas.createPath({
                path: mapData.pathes[key].path
            });

            path.setFill(this.color);
            path.id = 'jqvmap' + map.index + '_' + key;
            map.countries[key] = path;

            jQuery(this.rootGroup).append(path);

            path.setAttribute('class', 'jqvmap-region');

            if (params.selectedRegion !== null) {
                if (key.toLowerCase() == params.selectedRegion.toLowerCase()) {
                    path.setFill(params.selectedColor);
                }
            }
        }

        jQuery(params.container).delegate(this.canvas.mode == 'svg' ? 'path' : 'shape', 'mouseover mouseout', function (e) {
            var path = e.target,
                code = e.target.id.split('_').pop(),
                labelShowEvent = $.Event('labelShow.jqvmap'),
                regionMouseOverEvent = $.Event('regionMouseOver.jqvmap');

            if (e.type == 'mouseover') {
                jQuery(params.container).trigger(regionMouseOverEvent, [code, mapData.pathes[code].name]);
                if (!regionMouseOverEvent.isDefaultPrevented()) {
                    if (params.hoverOpacity) {
                        path.setOpacity(params.hoverOpacity);
                    }
                    else if (params.hoverColor) {
                        path.currentFillColor = path.getFill() + '';
                        path.setFill(params.hoverColor);
                    }
                }
                if (params.showTooltip) {
                    map.label.text(mapData.pathes[code].name);
                    jQuery(params.container).trigger(labelShowEvent, [map.label, code]);

                    if (!labelShowEvent.isDefaultPrevented()) {
                        map.label.show();
                        map.labelWidth = map.label.width();
                        map.labelHeight = map.label.height();
                    }
                }
            }
            else {
                path.setOpacity(1);
                if (path.currentFillColor) {
                    path.setFill(path.currentFillColor);
                }

                map.label.hide();
                jQuery(params.container).trigger('regionMouseOut.jqvmap', [code, mapData.pathes[code].name]);
            }
        });

        jQuery(params.container).delegate(this.canvas.mode == 'svg' ? 'path' : 'shape', 'click', function (e) {

            for (var key in mapData.pathes) {
                map.countries[key].currentFillColor = map.countries[key].getOriginalFill();
                map.countries[key].setFill(map.countries[key].getOriginalFill());
            }

            var path = e.target;
            var code = e.target.id.split('_').pop();

            jQuery(params.container).trigger('regionClick.jqvmap', [code, mapData.pathes[code].name]);

            path.currentFillColor = params.selectedColor;
            path.setFill(params.selectedColor);

        });

        if (params.showTooltip) {
            params.container.mousemove(function (e) {
                if (map.label.is(':visible')) {
                    map.label.css({
                        left: e.pageX - 15 - map.labelWidth,
                        top: e.pageY - 15 - map.labelHeight
                    });
                }
            });
        }

        this.setColors(params.colors);

        this.canvas.canvas.appendChild(this.rootGroup);

        this.applyTransform();

        this.colorScale = new ColorScale(params.scaleColors, params.normalizeFunction, params.valueMin, params.valueMax);

        if (params.values) {
            this.values = params.values;
            this.setValues(params.values);
        }

        this.bindZoomButtons();

        WorldMap.mapIndex++;
    };

    WorldMap.prototype = {
        transX: 0,
        transY: 0,
        scale: 1,
        baseTransX: 0,
        baseTransY: 0,
        baseScale: 1,
        width: 0,
        height: 0,
        countries: {},
        countriesColors: {},
        countriesData: {},
        zoomStep: 1.4,
        zoomMaxStep: 4,
        zoomCurStep: 1,

        setColors: function (key, color) {
            if (typeof key == 'string') {
                this.countries[key].setFill(color);
                this.countries[key].setAttribute("original", color);
            }
            else {
                var colors = key;

                for (var code in colors) {
                    if (this.countries[code]) {
                        this.countries[code].setFill(colors[code]);
                        this.countries[code].setAttribute("original", colors[code]);
                    }
                }
            }
        },

        setValues: function (values) {
            var max = 0,
                min = Number.MAX_VALUE,
                val;

            for (var cc in values) {
                val = parseFloat(values[cc]);
                if (val > max) {
                    max = values[cc];
                }
                if (val && val < min) {
                    min = val;
                }
            }

            this.colorScale.setMin(min);
            this.colorScale.setMax(max);

            var colors = {};
            for (cc in values) {
                val = parseFloat(values[cc]);
                if (val) {
                    colors[cc] = this.colorScale.getColor(val);
                }
                else {
                    colors[cc] = this.color;
                }
            }
            this.setColors(colors);
            this.values = values;
        },

        setBackgroundColor: function (backgroundColor) {
            this.container.css('background-color', backgroundColor);
        },

        setScaleColors: function (colors) {
            this.colorScale.setColors(colors);

            if (this.values) {
                this.setValues(this.values);
            }
        },

        setNormalizeFunction: function (f) {
            this.colorScale.setNormalizeFunction(f);

            if (this.values) {
                this.setValues(this.values);
            }
        },

        resize: function () {
            var curBaseScale = this.baseScale;
            if (this.width / this.height > this.defaultWidth / this.defaultHeight) {
                this.baseScale = this.height / this.defaultHeight;
                this.baseTransX = Math.abs(this.width - this.defaultWidth * this.baseScale) / (2 * this.baseScale);
            }
            else {
                this.baseScale = this.width / this.defaultWidth;
                this.baseTransY = Math.abs(this.height - this.defaultHeight * this.baseScale) / (2 * this.baseScale);
            }
            this.scale *= this.baseScale / curBaseScale;
            this.transX *= this.baseScale / curBaseScale;
            this.transY *= this.baseScale / curBaseScale;
        },

        reset: function () {
            this.countryTitle.reset();
            for (var key in this.countries) {
                this.countries[key].setFill(WorldMap.defaultColor);
            }
            this.scale = this.baseScale;
            this.transX = this.baseTransX;
            this.transY = this.baseTransY;
            this.applyTransform();
        },

        applyTransform: function () {
            var maxTransX, maxTransY, minTransX, minTransY;
            if (this.defaultWidth * this.scale <= this.width) {
                maxTransX = (this.width - this.defaultWidth * this.scale) / (2 * this.scale);
                minTransX = (this.width - this.defaultWidth * this.scale) / (2 * this.scale);
            }
            else {
                maxTransX = 0;
                minTransX = (this.width - this.defaultWidth * this.scale) / this.scale;
            }

            if (this.defaultHeight * this.scale <= this.height) {
                maxTransY = (this.height - this.defaultHeight * this.scale) / (2 * this.scale);
                minTransY = (this.height - this.defaultHeight * this.scale) / (2 * this.scale);
            }
            else {
                maxTransY = 0;
                minTransY = (this.height - this.defaultHeight * this.scale) / this.scale;
            }

            if (this.transY > maxTransY) {
                this.transY = maxTransY;
            }
            else if (this.transY < minTransY) {
                this.transY = minTransY;
            }
            if (this.transX > maxTransX) {
                this.transX = maxTransX;
            }
            else if (this.transX < minTransX) {
                this.transX = minTransX;
            }
            this.canvas.applyTransformParams(this.scale, this.transX, this.transY);
        },

        makeDraggable: function () {
            var mouseDown = false;
            var oldPageX, oldPageY;
            var self = this;

            this.container.mousemove(function (e) {

                if (mouseDown) {
                    var curTransX = self.transX;
                    var curTransY = self.transY;

                    self.transX -= (oldPageX - e.pageX) / self.scale;
                    self.transY -= (oldPageY - e.pageY) / self.scale;

                    self.applyTransform();

                    oldPageX = e.pageX;
                    oldPageY = e.pageY;
                }

                return false;

            }).mousedown(function (e) {

                mouseDown = true;
                oldPageX = e.pageX;
                oldPageY = e.pageY;

                return false;

            }).mouseup(function () {

                mouseDown = false;
                return false;

            });
        },

        bindZoomButtons: function () {
            var map = this;
            var sliderDelta = (jQuery('#zoom').innerHeight() - 6 * 2 - 15 * 2 - 3 * 2 - 7 - 6) / (this.zoomMaxStep - this.zoomCurStep);

            this.container.find('.jqvmap-zoomin').click(function () {
                if (map.zoomCurStep < map.zoomMaxStep) {
                    var curTransX = map.transX;
                    var curTransY = map.transY;
                    var curScale = map.scale;

                    map.transX -= (map.width / map.scale - map.width / (map.scale * map.zoomStep)) / 2;
                    map.transY -= (map.height / map.scale - map.height / (map.scale * map.zoomStep)) / 2;
                    map.setScale(map.scale * map.zoomStep);
                    map.zoomCurStep++;

                    jQuery('#zoomSlider').css('top', parseInt(jQuery('#zoomSlider').css('top'), 10) - sliderDelta);
                }
            });

            this.container.find('.jqvmap-zoomout').click(function () {
                if (map.zoomCurStep > 1) {
                    var curTransX = map.transX;
                    var curTransY = map.transY;
                    var curScale = map.scale;

                    map.transX += (map.width / (map.scale / map.zoomStep) - map.width / map.scale) / 2;
                    map.transY += (map.height / (map.scale / map.zoomStep) - map.height / map.scale) / 2;
                    map.setScale(map.scale / map.zoomStep);
                    map.zoomCurStep--;

                    jQuery('#zoomSlider').css('top', parseInt(jQuery('#zoomSlider').css('top'), 10) + sliderDelta);
                }
            });
        },

        setScale: function (scale) {
            this.scale = scale;
            this.applyTransform();
        },

        getCountryPath: function (cc) {
            return jQuery('#' + cc)[0];
        }
    };

    WorldMap.xlink = "http://www.w3.org/1999/xlink";
    WorldMap.mapIndex = 1;
    WorldMap.maps = {};

    var ColorScale = function (colors, normalizeFunction, minValue, maxValue) {
        if (colors) {
            this.setColors(colors);
        }
        if (normalizeFunction) {
            this.setNormalizeFunction(normalizeFunction);
        }
        if (minValue) {
            this.setMin(minValue);
        }
        if (minValue) {
            this.setMax(maxValue);
        }
    };

    ColorScale.prototype = {
        colors: [],

        setMin: function (min) {
            this.clearMinValue = min;

            if (typeof this.normalize === 'function') {
                this.minValue = this.normalize(min);
            }
            else {
                this.minValue = min;
            }
        },

        setMax: function (max) {
            this.clearMaxValue = max;
            if (typeof this.normalize === 'function') {
                this.maxValue = this.normalize(max);
            }
            else {
                this.maxValue = max;
            }
        },

        setColors: function (colors) {
            for (var i = 0; i < colors.length; i++) {
                colors[i] = ColorScale.rgbToArray(colors[i]);
            }
            this.colors = colors;
        },

        setNormalizeFunction: function (f) {
            if (f === 'polynomial') {
                this.normalize = function (value) {
                    return Math.pow(value, 0.2);
                };
            }
            else if (f === 'linear') {
                delete this.normalize;
            }
            else {
                this.normalize = f;
            }
            this.setMin(this.clearMinValue);
            this.setMax(this.clearMaxValue);
        },

        getColor: function (value) {
            if (typeof this.normalize === 'function') {
                value = this.normalize(value);
            }

            var lengthes = [];
            var fullLength = 0;
            var l;

            for (var i = 0; i < this.colors.length - 1; i++) {
                l = this.vectorLength(this.vectorSubtract(this.colors[i + 1], this.colors[i]));
                lengthes.push(l);
                fullLength += l;
            }

            var c = (this.maxValue - this.minValue) / fullLength;

            for (i = 0; i < lengthes.length; i++) {
                lengthes[i] *= c;
            }

            i = 0;
            value -= this.minValue;

            while (value - lengthes[i] >= 0) {
                value -= lengthes[i];
                i++;
            }

            var color;
            if (i == this.colors.length - 1) {
                color = this.vectorToNum(this.colors[i]).toString(16);
            }
            else {
                color = (this.vectorToNum(this.vectorAdd(this.colors[i], this.vectorMult(this.vectorSubtract(this.colors[i + 1], this.colors[i]), (value) / (lengthes[i]))))).toString(16);
            }

            while (color.length < 6) {
                color = '0' + color;
            }
            return '#' + color;
        },

        vectorToNum: function (vector) {
            var num = 0;
            for (var i = 0; i < vector.length; i++) {
                num += Math.round(vector[i]) * Math.pow(256, vector.length - i - 1);
            }
            return num;
        },

        vectorSubtract: function (vector1, vector2) {
            var vector = [];
            for (var i = 0; i < vector1.length; i++) {
                vector[i] = vector1[i] - vector2[i];
            }
            return vector;
        },

        vectorAdd: function (vector1, vector2) {
            var vector = [];
            for (var i = 0; i < vector1.length; i++) {
                vector[i] = vector1[i] + vector2[i];
            }
            return vector;
        },

        vectorMult: function (vector, num) {
            var result = [];
            for (var i = 0; i < vector.length; i++) {
                result[i] = vector[i] * num;
            }
            return result;
        },

        vectorLength: function (vector) {
            var result = 0;
            for (var i = 0; i < vector.length; i++) {
                result += vector[i] * vector[i];
            }
            return Math.sqrt(result);
        }
    };

    ColorScale.arrayToRgb = function (ar) {
        var rgb = '#';
        var d;
        for (var i = 0; i < ar.length; i++) {
            d = ar[i].toString(16);
            rgb += d.length == 1 ? '0' + d : d;
        }
        return rgb;
    };

    ColorScale.rgbToArray = function (rgb) {
        rgb = rgb.substr(1);
        return [parseInt(rgb.substr(0, 2), 16), parseInt(rgb.substr(2, 2), 16), parseInt(rgb.substr(4, 2), 16)];
    };

})(jQuery);

/** France Map Data Points for jQVMap http://jqvmap.com */
/** Map created by Gaël Jaffredo <http://www.jaffredo.com/> */
jQuery.fn.vectorMap('addMap', 'france_fr', {
    "width": 702,
    "height": 610,
    "pathes": {
        "80": {"path": "m424.3,82.9l3.3,2.6l-0.4,3.8l-3.9,8.1l2.3,7 l-10.3,1.5l-8.6,6.8l-12.8-6.7l-14.4-2.2l-3.1,1.2l-4.1-3.7l-3.4-8.4l-11.79-9.99L359.5,81l3.4-6.6l1.9-1.1l0.1-0.1l1.4,1.8l3.5,0.3 l-5.6-6l1.2-5.1l2.9,0.7l-0.03-0.02l10.53,1.02l1,3l7.1,5.8l12-2.5l-2.6,5.7L398,81l2.5-3.1l8.4,3.5l0.8-2.8l2.8,4.6l2.4-1.7 l0.8,3.2L424.3,82.9z", "name": "Somme"},
        "02": {"path": "m450.3,82.6l16.7,4.6l2.91,0.94L470.6,94l-1.3,3.5l1.3,3.1l-5,7.2 l-2.7,0.3l0.3,14.3l-1,2.8l-5.3-1.8l-8,4l-1.2,2.6l3.2,8l-5.5,2.3l1.6,2.4l-0.8,2.7l2.5,1.3l-7.7,10.2l-9.3-6l-3.9-4.2l0.7-2.8 l-1.8-2.5l-2.6-0.7l2.1-1.7l-0.5-2.8l-2.9-1.1l-2.4,1.5l-0.7-2.9l3,0.2l-2.9-4.5l2.6-1.7l2.4-5.7l2.6-1.1l-2.2-1.8l0.8-4.5 l-0.4-10.2l-2.3-7l3.9-8.1l0.4-3.8l12.6-0.6l2.6-2.2l2.3,1.7L450.3,82.6z", "name": "Aisne"},
        "77": {"path": "m441.1,176.1l-2.9,0.8l0.4,8.5l-15.4,3 l-0.2,5.8l-3.9,5.4l-11.2,2.7l-9.2-0.7l2.6-1.5l0.6-2.7l-4.2-4.3L397,190l3.4-4.8l4-17.2l-0.5-1l1.1-4.1l-0.3-2.9v-0.1l-1.3-4.7 l1.3-2.5l-1.7-5.1l0.1-0.1l1.7-2.3l-0.2-2l6.9,1l2-2.2l2.5,1.6l8.1-2.9l2.6,0.7l1.8,2.5l-0.7,2.8l3.9,4.2l9.3,6l-0.4,2l-2.6,2.2 l3.5,8.3l2.6,1.7L441.1,176.1z", "name": "Seine et Marne"},
        "12": {"path": "m430.8,440.7l9.4,4.5l-2,3.9l-2.8,1.1l8.4,4.1 l-4.3,5.3l0.3,1.5l-3.7,1l-3,5.3l-6.3-1.3l-0.1,8.7l-5.7-0.1l-1.3-2.8l-11.1-1.3l-4.2-5l-4.3-11.5l-4.8-4.3L385,444l-6.1,2.8 l-4.3-3.6l2.3-2.4l-3.1-2.7l0.4-3l-0.8-9.1l7.6-5l5.9-1.4l1.7-1.5h0.1l5.1-3.2l6.4,1.5l3.8-4.8l3-9.1l4.7-4.2l5.2,4l1.3,4.2l2.4,1.6 l-0.5,3l2.6,5.1v0.1l4.2,4.5l2.9,8.8l-0.5,8.7L430.8,440.7z", "name": "Aveyron"},
        "47": {"path": "m293.8,455.6v0.1l-0.7-2.6l3.6-5.2L285,445 l-0.4-4.6l4.5-4l-1.5-2.5l3-2.3l0.6-8.9l8.5-8.6l-2.9-1.4l1.1-3.3l2.7,0.9l1.2-2.5l3.1,0.1l2.8,6.4l8.9-0.5l2.8-2l2.2,1.8l6.1-1 l1.5,6l5.4-2.7l4.2,4.2l-3.4,3.1l2.7,9.1l-7.5,2v2.9l2.4,1.4l-4.4,5.5l1.3,2.7l-2.8-0.2l-3.6,4.7l-2.7,1.3l-8.6-1l-5,2.9l-8.3-0.7 l-1.4,2.5L293.8,455.6z", "name": "Lot et Garonne"},
        "81": {"path": "m419.7,471.9l1.3,2.8c-2.2,1.5-4.5,2.9-6.8,4.4 l-6.1-2.2l0.7,9.7l-0.8,3l-3.4,2l-12-1.4l-2.8,0.5l-1.1,3.3l-6.3-0.9l-1.8-2.3l-1-2.8l-14.3-9.3l0.7-5.4l-2.3-2.1l-0.2-2.8l-2.6-1.2 l-1.4-6.3l0.5-2.8l4.8-3.2l1-2.7L364,450l3-1.1l2.7,1.1l9.2-3.2l6.1-2.8l10.3,5.8l4.8,4.3l4.3,11.5l4.2,5L419.7,471.9z", "name": "Tarn"},
        "07": {"path": "m496.5,434.2l0.1,3.7l-6-3.5h-2.8l-2.5,2.3 l-0.1-3.1l-7.1,4.3l-7.8-5l-2.7-5.9l-4.2-8.3l-2.1-9.1l6.7-6.4l5.9-1.9l3.4-5.9l3.4-0.4l-0.7-2.8l2.6-2.3l1.5-5.2l2.6,1.2v-3.1 l0.9-4.1l3.5-0.8l3.2-4.9l5-2.7l2,4.2l0.5,10.3l3.8,11.3l-1.5,6.2l-3.5,4.5l1,7.1l-3,5.9L496.5,434.2z", "name": "Ardèche"},
        "90": {"path": "m580.3,215.9l0.9-0.6l7.6,5l0.5,9l2.8-0.2l2,5 l-0.1,0.1l-2.79,0.39l-1.11-0.39l-3.19,4.34L586.5,239l-1.4-2.4l1.4-2.5l-5.9-2.7h-0.1l-1.4-5.5l-1.1-4.3L580.3,215.9z", "name": "Territoire de Belfort"},
        "48": {"path": "m463.4,418.7l4.2,8.3l-1.1,2.1l-3.6,2.5 l3.9,7.7l-1.2,2.9l1.4,3l-6.3,1.8l-8.7-2.2l-0.1,3.3l-3.9,0.5l-7.8-3.4l-9.4-4.5l-1.5-2.4l0.5-8.7l-2.9-8.8l-4.2-4.5v-0.1l6.9-15.9 l1.7,2.3l6.8-5.7l1-1l2.3,1.7l1.5,5.7l6.4,1.2l0.1-2.8l2.9,0.2l9,7.7L463.4,418.7z", "name": "Lozère"},
        "84": {"path": "m541,463.4l6.1,6l-0.1,0.4l-0.1-0.1l-6.6,4.3 l-3.2,0.2l-12-4.8l-3.5,0.7l-4.5-2.3l-5.5-5.7l-10.4-2.9l4.5-5l-6.3-6.4l-0.2-5.5l-2.6-4.4l-0.1-3.7l5.9,0.7l3.5,4.2l8.7-3.9 l2.4,1.4l2.5-2.2l0.5,5.8l9.3,0.9l0.1,2.8l5.2,2.3l2.2,5.4l-1.2,3l3.7,5.2l-1.7,2.5L541,463.4z", "name": "Vaucluse"},
        "01": {"path": "m542,347l-5.7,6.7l-11.2-15.2l-2.8,0.7l-3,5.1 l-6-2l-6.4,0.5l-3.7-5.7l-2.8,0.5l-3.1-9.2l1.5-8l5.9-20.9l5.9,1.5l5.4-1.3l4.8,3.3l4.3,7.7h2.9l0.1,3l2.9-0.1l4-4.4l3.4,1.6 l0.4,2.8l3.8-0.2l5.5-3.2l5.3-7.2l4.5,2.7l-1.8,4.7l0.3,2.5l-4.4,1.5l-1.9,2l0.2,2.8l0.46,0.19l-4.36,4.71h-2.9l0.8,9.3L542,347z", "name": "Ain"},
        "73": {"path": "m603.7,362l-1,10.3l-3.1,1.4l-2.2,0.7l-4.5,3.4 l-1.5,2.4l-2.5-1.4l-5.1,1.3l-2,1.8v0.1l-6.8,1.9l-2,2l-7.7-3.5l-5.2-1.5l-1-5.4l2.3-5.5l-2.7-6.3l-9.4-2.1l-0.3,2.8l-6.2-0.2 l-6.5-10.5l5.7-6.7l2.3-13.6l2.7,6.7l2.7,0.9l1.3,2.5l3,1.7l2.6-1.6l3.2,0.8l4.6,3.6l9.4-13.9l2.4,1.6l-0.6,3l2.3,1.8l6.2,2.3 l2.2-1.5l0.62-0.76l1.88,4.66l2.7,1.1l1.5,1.9l2.8,0.4l-0.7,3l1.3,5.2l5.1,4L603.7,362z", "name": "Savoie"},
        "74": {"path": "m547,340.1l-2.7-6.7l-0.8-9.3h2.9l4.36-4.71 l2.24,0.91l2.3-1l2.3,0.1l3.4-3.5l2.1-1l1-2.3l-2.8-1.3l1.8-5.1l2.4-0.8l2.3,1l3.6-2.9l9.5-1.3l3.2,0.6l-0.5,2.7l4.2,4.1l-2.1,6.4 l-0.6,1.5l4.6,1.7l-0.1,4.8l2-1.4l4.6,6.6l-1.3,5l-2.5,1.7l-4.9,0.9l-0.6,3.7l0.02,0.04l-0.62,0.76l-2.2,1.5l-6.2-2.3l-2.3-1.8 l0.6-3l-2.4-1.6l-9.4,13.9l-4.6-3.6l-3.2-0.8l-2.6,1.6l-3-1.7l-1.3-2.5L547,340.1z", "name": "Haute Savoie"},
        "39": {"path": "m552.3,291.4l3.68,3.49L553.4,303l-5.3,7.2 l-5.5,3.2l-3.8,0.2l-0.4-2.8l-3.4-1.6l-4,4.4l-2.9,0.1l-0.1-3h-2.9l-4.3-7.7l2.8-1.1l-0.8-5.3l2.8-5l-2.2-8.7l-2.5-1.6l5-3.7 l-8.3-4.4l-0.4-2.9l1.1-4.1l6.2-6.8l1.8-8.2l1.2-1.3l2.3,2l5.4,0.1l5.2,7.8l-3,5.1l1.2,2.5l2-2.1l1.5,2.5l3-0.8l5.8,10.1l2.9,0.5 l4.2,4.1l-2.1,2.3L552.3,291.4z", "name": "Jura"},
        "25": {"path": "m590.1,245.2l-2.4,2.2l0.4,3l-4.8,6.2l-4.8,4 l-0.4,2.9l-2.5,2.7l-5.7,1.7l-0.3,0.3l-1.7,2.3l0.9,2.7l-0.7,4.5l0.5,2.5l-9.5,8.8l-2.9,5.2l-0.22,0.69l-3.68-3.49l3.6-7.4l2.1-2.3 l-4.2-4.1l-2.9-0.5l-5.8-10.1l-3,0.8l-1.5-2.5l-2,2.1l-1.2-2.5l3-5.1l-5.2-7.8l22.3-10.2l3-4.7l5.6-1.9l2.8,0.9l1.8-2.2l3.2-0.4 l0.5-2.8l5.9,0.8l0.2-0.1h0.1l5.9,2.7l-1.4,2.5l1.4,2.4l0.41-0.46l-0.11,0.16l-2.2,4.9l7-0.7L590.1,245.2z", "name": "Doubs"},
        "04": {"path": "m596.5,409.9l0.57-0.5l-0.37,4.5l-2.2,1.5 l-0.6,2.9l3.5,4l-1.8,4.8l0.19,0.21L589,435.1l-2,5.3l4.3,8.5l7,7.7l-5.2-0.6l-5.2,3.8l1.2,2.6l-3,1.4l-9.8,0.4l-1.2,3.5l-5.9-3.6 l-10.1,8.5l-4-4.8l-2.7,1.8l-5.3-0.2l-6.1-6l-3.4-1.1l1.7-2.5l-3.7-5.2l1.2-3l-2.2-5.4l4.3-4.8l2.3-0.1l1-0.2l5.9-1.4l3.8,1 l-3.4-4.9l3.9,1.1l1.4-8.6l5.3-4l3.3-0.7l3.5,4.5l0.7-3.8l3.8-4.2l11.1,3.3l9-10.2L596.5,409.9z", "name": "Alpes de Haute Provence"},
        "05": {"path": "m597.1,409l-0.03,0.4l-0.57,0.5l-6,3.3l-9,10.2 l-11.1-3.3l-3.8,4.2l-0.7,3.8l-3.5-4.5l-3.3,0.7l-5.3,4l-1.4,8.6l-3.9-1.1l3.4,4.9l-3.8-1l-5.9,1.4l0.2-4.7l-10-5.7l-1.5-2.6 l3.2-5.1l4.2,1.4l2.5-2.5l-3-2.3l2.5-6.7l5.8-0.3l0.3-3.4l1-3.1l5.5-1.2l0.9-2.8l12.6-3.9l3.1,1.1l0.4-5.8l-4.4-3.1l-1.8-4.2 l1.6-3.8l7.7,3.5l2-2l6.8-1.9l1.8,4.5l2.4,0.6l1.1,2l0.4,3l1.2,2.2l3,2.3l5.7,0.5l2.2,1.3l-0.7,2.1l3.2,4.7l-3,1.5L597.1,409z", "name": "Hautes Alpes"},
        "32": {"path": "m330.6,461.7l2,6.9l9.1,13.3l-6.5,6.1l-0.7,5.8 l-11.4-2.5l-3.5,5.3l-3.1,1.1l-12.4-2.2l-1.4-3l-5.5,0.6l-2.6-8.7l-3.3-1.3l-2-3.5l-3.9,0.5l-6.6-0.3l-1.6-2.7l4.2-11.1l-0.5-6.4 l9.4-2.7l0.6,3l2.5-1.3l0.4-2.9v-0.1l3.7,0.7l1.4-2.5l8.3,0.7l5-2.9l8.6,1l2.7-1.3l5.3,1.7l-3.3,4.6L330.6,461.7z", "name": "Gers"},
        "24": {"path": "m307.7,414.3l-2.8-6.4l-1-1.3l0.9-2.9l-2.4-2.6l-2,3.2l-9.8-2.3l2-2 l0.2-5.7l2.8-5.5l-1.2-2.8l-3.7,0.6l2-5l0.1-0.4l2-2l5.5-0.7l4.1-5.8l0.7-5.7l7.9-5.9l2.1-5.3l5.7-6.1l6.2,3l-0.1,4.7l9.5-1.1 l7.2,5.6l-2,2.7l5.7,2.2l2.9,4.7l-3.6,4.6l1.7,3l-2,1.8l2.3,1.5l0.2,3l3.2,0.7l3.5,7l-0.7,5l-1.4,5.3l-4.5,3.2l0.6,3.6l-6,3.4 l-4.7,6.5l-4.2-4.2l-5.4,2.7l-1.5-6l-6.1,1l-2.2-1.8l-2.8,2L307.7,414.3z", "name": "Dordogne"},
        "51": {"path": "m440.6,158.9l0.4-2l7.7-10.2l-2.5-1.3l0.8-2.7 l-1.6-2.4l5.5-2.3l-3.2-8l1.2-2.6l8-4l5.3,1.8l1-2.8l6.7,0.9l9.8,6.6l20.1,1.9l2.2,9l-1,4.1l2.6,1.3l-0.6,3.9l-3.1,1.1l-1.1,5.8 l3.2,4.6l0.5,4.1l-8.6,2.2l2.2,2.5l-2.3,2.2l0.7,2.9h-4.7l-3.3-1.1l-2.5,1.5c-1.8-0.8-3.7-1.5-5.6-2.3l-2.6-2l-0.4-6.1l-3.1-0.5 l-6.5,0.9l-9.5,7.6v3.3l-9-1.2l-3.2-4.5l-2.6-1.7l-3.5-8.3L440.6,158.9z", "name": "Marne"},
        "60": {"path": "m372.8,131.1l-3.5-8.4l1.3-8l-1.4-8.7l3.1-4.7 l4.1,3.7l3.1-1.2l14.4,2.2l12.8,6.7l8.6-6.8l10.3-1.5l0.4,10.2l-0.8,4.5l2.2,1.8l-2.6,1.1l-2.4,5.7l-2.6,1.7l2.9,4.5l-3-0.2l0.7,2.9 l2.4-1.5l2.9,1.1l0.5,2.8l-2.1,1.7l-8.1,2.9l-2.5-1.6l-2,2.2l-6.9-1l-10.9-6.4l-2.2,1.6l-9.2-2.2L376,138l-5.6-1.1l-1.6-3.2 L372.8,131.1z", "name": "Oise"},
        "52": {"path": "m493.9,167.9l8.6-2.2l3.4,5.2l16.9,10.4 l-2.4,2.3l12.7,9.5l-1.7,8.6l5.5,4.7l0.2,3.1l2.7-1.1l1.3,2.5v0.1l0.2,1.4l-2.3,3.2l-2.9-0.3l-2,2.4l-0.3,8.3l-3.2,1l-2.1-1.8 l-6.6,3.9l-1.2,2.5l-4.8,1.9v-2.8l-3-1.6l-9.2-2l-2.3-4.8l2.8-2.4l-1-3.1l-1.8-2.2l-2.9-0.3l0.3-2.9l-2.6-1l-0.5-2.7l-3.5-0.7 l-0.2-4.8l5.6-2.5l0.1-2.7l-0.5-9.9l-5.6-3l-2.4-5.2l0.6-3.4h4.7l-0.7-2.9l2.3-2.2L493.9,167.9z", "name": "Haute Marne"},
        "88": {"path": "m520.4,183.6l2.4-2.3l6.9-3.4l3,0.5l1.9-2.3 l5.8,0.6l-0.1,2.9l4.1,4.8l5.7-0.7l1.1-2.8l5.4-2l2.4,1.8l6-1l5.3-3.8l1.6,2.7l6.1,1.6l10.6-7.6l1.5-0.4l-1.3,10l5.2,2.2l-1.1,1 l-4.1,13.5l-4.8,7.8v6.2l-2.8,2.4l-0.9,0.6l-8.4-6.6l-5.1,2.2l-4.9-3.6l-5.8,1.6l-7-4.3l-8,5.8v-0.1l-1.3-2.5l-2.7,1.1l-0.2-3.1 l-5.5-4.7l1.7-8.6L520.4,183.6z", "name": "Vosges"},
        "27": {"path": "m316.4,153.4l-0.2-3.2l0.7-2.2l-2.3-4.1l1.4-3l-2.8-9.4l2.3-2.1h-2.9 l-0.3-8.3l1.7-0.4l0.28-0.1h1.52l-0.9-0.2l0.8-0.3l-1.29-0.3l5.89-2.4l7.6,5l3.4-0.7l4.9,3l-1.9,2.4l2.1,2.1l5.4,2.4l1.4-2.7 l8.2-2.5l4.8-7l13.1,3.3l3.5,8.4l-4,2.6l-4,9.5l-3.8-0.1l-2.3,2.6l1.8,5.8l-6,6.9l0.2,2.8l-6,1.3l-2.8-1.6l-5.3,3.2l-6,1.1l-2.4,2.6 l-3.4-2.1l1.7-2.3l-7.8-9.5L316.4,153.4z", "name": "Eure"},
        "61": {"path": "m266.9,179.9l-3.3-3.7l3.6-4.5l1.8-6.7 l-4.1-4.2l5.4-2.9l0.9-2.7l2.7,1.3l6.4-2.3l4.9,3l2.6-1.7l5.7,1.1l2.9-0.3l7.2-5.4l7-1.7l5.6,1l0.2,3.2l6.3,0.5l7.8,9.5l-1.7,2.3 l3.4,2.1l0.1,3.2l4.8,4.4l-0.2,4.5l0.5,4.6l-7.5,5.1l1.1,7.5l-3.2-0.7l-3.1-3.5l-2.9,1l-7.2-5l-1.6-8.4l-2.8-1.5l-11,5.9l-3-0.1 v-0.1v-2.9l-3.3-1.6l-1.9-6l-2.7-0.2l-0.7,2.7h-9.1l-6.7,3.3l-2.5-1.7L266.9,179.9z", "name": "Orne"},
        "79": {"path": "m292.3,331.6l-2.7,1.6l-3.6-4.7l-17.4-6.7 l-5.9-6.5v-3.7l9.1-4.5l-2.5-2l0.2-7.4l-4.7-17.9l-4.2-4.1l-2.3-5.7l12.3-0.8l3.7-4.8l5.6-0.8l9.6-0.2l2.3,2.3l3.4,9l-0.8,3l2.7,1.2 l-4.5,14.1l2.7-0.9l1.5,3l-3.4,5.5l0.5,5.8l2.1,2l-0.1,2.8l6.4,0.2l-3.2,8.5l4.5,3l-0.8,2.8h-0.1l-0.8,0.8l-3.5-0.4l-5.8,2.5 L292.3,331.6z", "name": "Deux Sèvres"},
        "16": {"path": "m294.8,379.2l-2,2v-0.1l-6.3-6.3l-6-1.2l1.7-3l-2.3-2l2.4-1.7l-1.5-2.6 l1.7-2.6l-2.4-1.7l-0.3-3l-5-3.1l2.2-2.1l-3.2-5.6l8.1-3.3l2.3,2l2.7-0.1l2.7-11.6l2.7-1.6l0.3-3l5.8-2.5l3.5,0.4l0.8-0.8h0.1l9.1,3 l2.9-0.8l-1.4-2.4l2.2-1.8l4.1,3.9l3.8-1.4l1.3-2.5l4.8,0.6l-0.2,5.1l4.7,3.6l-0.6,3.2l-2.6,1.1l-4,8l-2.8,0.6l-3.4,3.8h0.1 l-5.7,6.1l-2.1,5.3l-7.9,5.9l-0.7,5.7l-4.1,5.8L294.8,379.2z", "name": "Charente"},
        "49": {"path": "m270.6,269.2l-12.3,0.8l-10.6-3.8l-0.4-3 l-2.8-1.4l2.8-4.5l-0.8-2.8l-3.6-4.7l-3-0.8l2.9-2.2l14.3-2.1l-0.3-5.9l-8.5-3.3l1.9-2.4l2.7,0.4l-7.2-13.3l0.4-2.2l10.5,3.5 l2.1-1.9l8.7,3.6l3,0.4l5.9-2.7l5.1,1.7l0.6,2.7l6.7-0.2l0.2,3.5l2,2l3.1-1.3l5.2,3.3l7.4,0.1l-0.7,2.4l-1.7,9.3l-5.8,15.3v0.1 l-6.6,5.9l-2.3-2.3l-9.6,0.2l-5.6,0.8L270.6,269.2z", "name": "Maine et Loire"},
        "53": {"path": "m256.6,221.5l-10.5-3.5l3.6-8.6l5.5-2.2 l-1.9-17.3l1.5-2.4l0.1-12.1l8.6,0.8h0.1l3.3,3.7l2.4-1.6l2.5,1.7l6.7-3.3h9.1l0.7-2.7l2.7,0.2l1.9,6l3.3,1.6v2.9v0.1l-4.3,2.7 l0.3,6.9l-4.4,4l1.2,2.9l-5,4.6l1.4,3.4l-5.5,7.7l1.5,5.6l-5.1-1.7l-5.9,2.7l-3-0.4l-8.7-3.6L256.6,221.5z", "name": "Mayenne"},
        "59": {"path": "m384.33,25.06l0.87-0.26l2,0.8l1.1-2.1l7.9-2.1 l2.9,0.3l4.4-1.9v-0.1l1.2,4.8l2.3,3.7l-1.6,1.9l0.6,0.8l1.2,5.8h3.4l2.7,5.1l3.1,1.5h2.1l0.6-2.4l8.1-3l3.8,7.5l0.1,1l1.3,5.2 l2,3.5h0.1l2.8,0.6l2.1-1.4l2.4-0.2l-0.5,2.2l2.2-0.7l2.8,1l1.8,4.4l-0.6,2.3l0.7,2.3l1.4,1.9l1.1-2.6l4.6-0.3l2.4,1.1L462,64l5.5,6 l2.3,0.2l-2.1,2.4l-1.4,4.7l2.6,0.2l1.4,3.3l-3.5,3.9l0.2,2.5l-16.7-4.6l-5.2,1.8l-2.3-1.7l-2.6,2.2l-12.6,0.6l-3.3-2.6l3.5-10.6 l-1.8-2.4l-3-0.4l0.7-2.7l-3.9-5.2l3.1-1.6l-3.8-5.3l-5.9-1l1-6.1l-1.3-2.5l-1.7,2.2l-11.6-0.5l-4.1-4.2l0.6-2.8l-5.5-2.6 L384.33,25.06z", "name": "Nord"},
        "62": {"path": "m379.8,68.9l7.1,5.8l12-2.5l-2.6,5.7L398,81 l2.5-3.1l8.4,3.5l0.8-2.8l2.8,4.6l2.4-1.7l0.8,3.2l8.6-1.8l3.5-10.6l-1.8-2.4l-3-0.4l0.7-2.7l-3.9-5.2l3.1-1.6l-3.8-5.3l-5.9-1 l1-6.1l-1.3-2.5l-1.7,2.2l-11.6-0.5l-4.1-4.2l0.6-2.8l-5.5-2.6l-6.27-12.14L372.6,28.5l-6.4,5.4l0.9,5.6l-1.7,4.6l0.6,6.7l2,4.2 l-1.7-1.4l-0.3,9.7l2.27,1.58l10.53,1.02L379.8,68.9z", "name": "Pas-de-Calais"},
        "08": {"path": "m469.91,88.14l0.79,0.26l9.8,0.4l7.3-3.2l1.1-6 l4-3.8l2.8-0.2v3.8L494,81l-0.6,5.2l3.3,4.5l-1,2.4l0.6,3.1l1.4,1.9l3.3-0.9l4.3,2.4l2.8,3.8l4.9,0.6l2,1.7l-0.9,2.4l2.1-0.13 l-1.6,1.13l-2,2.7l-5.7-2.1l-1.9,2l0.8,8.8l-3.2,5.1l1.4,2.5l-4.2,3.6v0.1l-20.1-1.9l-9.8-6.6l-6.7-0.9l-0.3-14.3l2.7-0.3l5-7.2 l-1.3-3.1l1.3-3.5L469.91,88.14z", "name": "Ardennes"},
        "54": {"path": "m588.2,170.9l1.9,1.3l-1.5,0.4l-10.6,7.6l-6.1-1.6l-1.6-2.7l-5.3,3.8 l-6,1l-2.4-1.8l-5.4,2l-1.1,2.8l-5.7,0.7l-4.1-4.8l0.1-2.9l-5.8-0.6l0.2-2.9l-2.5-2l1.7-2.8l-1.3-8.6l2.2-13.8l0.9-2.7l-4.9-11.5 l1.5-5.9l-1.2-2.7l-4.4-4.8l-5.3,2l-0.7-5.3l4.8-1.7l2-1.9h6.8l2.54,2.31L539.6,124l2.5,1.6l1.2,3.6l-1.7,3.1l1,5.6l-2.8,0.1 l4.3,7.5l11.5,4l-0.3,2.9l2.7,5.1l8.5,1.5l5.3,3.9l14.4,5.3L588.2,170.9z", "name": "Meurthe et Moselle"},
        "57": {"path": "m539.6,124l-2.65-10.19l0.65,0.59h2.4l1.5,2.1 l2.3,0.7l2.3-0.5l1-2.3l2-1.2l2.2-0.2l4.5,2.3l4.9-0.1l3.1,3.8l2.3,1.9l-0.5,2l3.7,3.2l2.8,4.5v2.3l4.2,0.7l1.2-1.9l-0.3-2.4 l2.6-0.2l3.8,1.8l1.4,3.5l2.1-1.5l2.5,1.9l5.8-0.4l5.3-4.2l2.2,1.4l0.5,2.1l2.4,2.4l3.2,1.5h0.03l-1.73,4.4l-1.4,2.6l-8.9,0.3 l-9.1-4.6l-0.8-2.8l-5,10.8l5.5,2.4l-1.6,2.5l2.3,1.7l1.3-2.5l3,0.3l4.3,3.4l-3,13.3l-2.3,1.8l-3.4-0.3l-2-2.7l-14.4-5.3l-5.3-3.9 l-8.5-1.5l-2.7-5.1l0.3-2.9l-11.5-4l-4.3-7.5l2.8-0.1l-1-5.6l1.7-3.1l-1.2-3.6L539.6,124z", "name": "Moselle"},
        "67": {"path": "m631.8,140.7l-2.8,9.4l-7.8,10.5l-2,1.5l-1.4,3.3l0.3,4.9l-2.4,7.2 l0.7,3.6l-1.5,2l-1.2,5.5l-3.16,6.23L605.9,193l-0.3-2.8l-8.5-5.6l-3.1-0.2l-5.2-2.2l1.3-10l-1.9-1.3l3.4,0.3l2.3-1.8l3-13.3 l-4.3-3.4l-3-0.3l-1.3,2.5l-2.3-1.7l1.6-2.5l-5.5-2.4l5-10.8l0.8,2.8l9.1,4.6l8.9-0.3l1.4-2.6l1.73-4.4l8.87,0.6l2.4-0.6 L631.8,140.7z", "name": "Bas Rhin"},
        "68": {"path": "m605.9,193l4.64,1.83l-0.04,0.07v5.3l1.6,1.9 l0.2,3.4l-2.2,11.1l0.1,6.7l1.8,1.5l0.6,3.5l-2.2,2l-0.2,2.3l-3.1,0.9l0.5,2.2l-1.5,1.6h-2.7l-3.8,1.4l-3-1.1l0.3-2.5l-2.4-1.1 l-0.4,0.1l-2-5l-2.8,0.2l-0.5-9l-7.6-5l2.8-2.4v-6.2l4.8-7.8l4.1-13.5l1.1-1l3.1,0.2l8.5,5.6L605.9,193z", "name": "Haut Rhin"},
        "55": {"path": "m516.2,107.97l1.2-0.07l1.5,1.6l1.9,5.6 l0.7,5.3l5.3-2l4.4,4.8l1.2,2.7l-1.5,5.9l4.9,11.5l-0.9,2.7l-2.2,13.8l1.3,8.6l-1.7,2.8l2.5,2l-0.2,2.9l-1.9,2.3l-3-0.5l-6.9,3.4 l-16.9-10.4l-3.4-5.2l-0.5-4.1l-3.2-4.6l1.1-5.8l3.1-1.1l0.6-3.9l-2.6-1.3l1-4.1l-2.2-9v-0.1l4.2-3.6l-1.4-2.5l3.2-5.1l-0.8-8.8 l1.9-2l5.7,2.1l2-2.7L516.2,107.97z", "name": "Meuse"},
        "87": {"path": "m348.9,364.1l-1.6,2.7l-5.7-2.2l2-2.7l-7.2-5.6 l-9.5,1.1l0.1-4.7l-6.2-3h-0.1l3.4-3.8l2.8-0.6l4-8l2.6-1.1l0.6-3.2l-4.7-3.6l0.2-5.1v-3.5l3-5l5.6-2.7l1.3-3l3.3,0.9l1.3-2.4 l0.8,0.3l2.6,1.1l5.8-1.1l1.7,2.5l1.2,2.6l-3,5.3l6.6,9.8l1.8,5.2l-0.9,3.4l2.9,1.5l0.1,2.8l6.4,0.5l4.7,3l0.6,5.9l-8.7,4l-4.9,4.6 l-5.7,1.3l-4.7,4L348.9,364.1z", "name": "Haute Vienne"},
        "18": {"path": "m385.3,235.4l5-2.4l13.5,3.1l3.9,4.8l9-1.7l2,6.5l-1.7,5.8l2.7,2.1 l3.1,7.6l0.3,5.9l2.2,2l-0.2,5.8l-1.3,8.9h-0.1h-4l-4.8,3.7l-8.4,2.9l-2.3,1.9l1.7,5.3l-1.7,2.4l-8.7,1l-3.5,5.9v0.1l-4.9-0.2 l1.5-3.5l-0.9-8.9l-4.7-7.9l1.4-2.7l-2.3-2.2l2.5-5.1l-2.3-11.7l-11.6-1.6l2.8-5.5l2.8,0.1l0.6-2.8l9.7-2l-2.1-5.9l5.9-4.1 L385.3,235.4z", "name": "Cher"},
        "03": {"path": "m443.1,292.3l5.9-6l6.7,13.5l7.9,2.9l1.6,2.4l-0.5,5.5l-3.7,4.6 l-3.9,1.3l-0.5,3l1.5,12.4l-5.5,4.8l-3.5-4.3l-6.4-0.4l-1.4-3.2l-13.1-0.5l-1.6-2.5l-3.3,0.5l-4.4-4.5l1.2-2.8l-2.3-1.7l-11.2,8 l-2.5-1.2l-3.6-8.4c-2.5-1.6-4.9-3.2-7.4-4.8L392,307v-0.1l3.5-5.9l8.7-1l1.7-2.4l-1.7-5.3l2.3-1.9l8.4-2.9l4.8-3.7h4h0.1l5.7,6.3 l6.4,0.2l2.8-1.7L443.1,292.3z", "name": "Allier"},
        "41": {"path": "m357.9,256.4l-6,3.2l-4.2-6.7l-2.6,1.3 l-2.4-1.9l1.2-9.1l-3.4-5.1l0.7-2.7l-6-3.4l-2.2,1.8l0.2-5.5l-10.5-0.5l0.6-3.5l3.2-1.1l6.3-10.6l-0.4-5.5l-1.7-2.2l2-2.1v-0.1 l6.3-1.3l12.8,10.8l5.5-3.5l3.3,1.9l2.5,7.1l-1.8,3.2l1.7,5.6l3-1.3l2.4,1.5l1.1,3.8l2.9,0.6l1.9-2.3l15.2,1.6l0.8,2.6l-5,2.4 l5.1,7.6l-5.9,4.1l2.1,5.9l-9.7,2l-0.6,2.8l-2.8-0.1l-5.2-3.5l-6.4,0.9l-1.5,2.6L357.9,256.4z", "name": "Loir-et-Cher"},
        "70": {"path": "m579.1,225.9l1.4,5.5l-0.2,0.1l-5.9-0.8 l-0.5,2.8l-3.2,0.4l-1.8,2.2l-2.8-0.9l-5.6,1.9l-3,4.7L535.2,252l-5.4-0.1l-2.3-2l-3.9-8.2l-2.6-1.4l4.6-4.1l-0.1-3.2l-2-3l-2.7,1.6 h-0.1l1.2-2.5l6.6-3.9l2.1,1.8l3.2-1l0.3-8.3l2-2.4l2.9,0.3l2.3-3.2l-0.2-1.4l8-5.8l7,4.3l5.8-1.6l4.9,3.6l5.1-2.2l8.4,6.6l-2.3,5.7 L579.1,225.9z", "name": "Haute Saône"},
        "23": {"path": "m396.6,343.5l4.4,5.5l-2.4,2.4l-2.7-0.8 l-2.3,2.6l-11.2-5.2l-7,3.4l-0.6-5.9l-4.7-3l-6.4-0.5l-0.1-2.8l-2.9-1.5l0.9-3.4l-1.8-5.2l-6.6-9.8l3-5.3l-1.2-2.6l2.8-2.9l11.5-1.1 l1.9-2.5l13.2,2.7l2.7-0.8l4.9,0.2l1.1,3.9c2.5,1.6,4.9,3.2,7.4,4.8l3.6,8.4l-0.5,4.1l2.3,6.7L396.6,343.5z", "name": "Creuse"},
        "36": {"path": "m357.8,308.5l-2.8,2.9l-1.7-2.5l-5.8,1.1 l-2.6-1.1l1.5-2.8l-2.5-1.3l-2.6-5.4h-2.9l-4.6-4.4l0.8-5.8l-2.1-3l5.6-0.5l-1-2.7l3.3-11.9l5.1-2.7l2.3,1.7l2.6-3.5l2.5-2.1l-1-4.9 l6-3.2l2.5,1.3l1.5-2.6l6.4-0.9l5.2,3.5l-2.8,5.5l11.6,1.6l2.3,11.7l-2.5,5.1l2.3,2.2l-1.4,2.7l4.7,7.9l0.9,8.9l-1.5,3.5l-2.7,0.8 l-13.2-2.7l-1.9,2.5L357.8,308.5z", "name": "Indre"},
        "971": {"path": "M35.87,487.13l0.7,7.2l-4.5-1.1l-2,1.7l-5.8-0.6l-1.7-1.2l4.9,0.5l3.2-4.4L35.87,487.13z M104.87,553.63 l-4.4-1.8l-1.9,0.8l0.2,2.1l-1.9,0.3l-2.2,4.9l0.7,2.4l1.7,2.9l3.4,1.2l3.4-0.5l5.3-5l-0.4-2.5L104.87,553.63z M110.27,525.53 l-6.7-2.2l-2.4-4.2l-11.1-2.5l-2.7-5.7l-0.7-7.7l-6.2-4.7l-5.9,5.5l-0.8,2.9l1.2,4.5l3.1,1.2l-1,3.4l-2.6,1.2l-2.5,5.1l-1.9-0.2 l-1,1.9l-4.3-0.7l1.8-0.7l-3.5-3.7l-10.4-4.1l-3.4,1.6l-2.4,4.8l-0.5,3.5l3.1,9.7l0.6,12l6.3,9l0.6,2.7c3-1.2,6-2.5,9.1-3.7l5.9-6.9 l-0.4-8.7l-2.8-5.3l0.2-5.5l3.6,0.2l0.9-1.7l1.4,3.1l6.8,2l13.8-4.9L110.27,525.53z", "name": "Guadeloupe"},
        "972": {"path": "m44.23,433.5l1.4-4.1l-6.2-7.5l0.3-5.8l4.8-4 l4.9-0.9l17,9.9l7,8.8l9.4-5.2l1.8,2.2l-2.8,0.8l0.7,2.6l-2.9,1l-2.2-2.4l-1.9,1.7l0.6,2.5l5.1,1.6l-5.3,4.9l1.6,2.3l4.5-1.5 l-0.8,5.6l3.7,0.2l7.6,19l-1.8,5.5l-4.1,5.1h-2.6l-2-3l3.7-5.7l-4.3,1.7l-2.5-2.5l-2.4,1.2l-6-2.8l-5.5,0.1l-5.4,3.5l-2.4-2.1 l0.2-2.7l-2-2l2.5-4.9l3.4-2.5l4.9,3.4l3.2-1.9l-4.4-4.7l0.2-2.4l-1.8,1.2l-7.2-1.1l-7.6-7L44.23,433.5z", "name": "Martinique"},
        "974": {"path": "m41.33,265.3l-6.7-8.5l1.3-6l4.1-2.4l0.7-7.9 l3.3,0.4l7.6-6.1l5.7-0.8l21,4l5,5.3v4.1l7.3,10.1l6.7,4.5l1,3.6l-3.3,7.9l0.9,9.6l-3.4,3.5l-17.3,2.9l-19.6-6.5l-3.8-3.6l-4.7-1.2 l-0.9-2.5l-3.6-2.3L41.33,265.3z", "name": "La Réunion"},
        "973": {"path": "m95.2,348.97l-11.7,16.4l0.3,2.4l-7.3,14.9 l-4.4,3.9l-2.6,1.3l-2.3-1.7l-4.4,0.8l0.7-1.8l-10.6-0.3l-4.3,0.8l-4.1,4.1l-9.1-4.4l6.6-11.8l0.3-6l4.2-10.8l-8.3-9.6l-2.7-8 l-0.6-11.4l3.8-7.5l5.9-5.4l1-4l4.2,0.5l-2.3-2l24.7,8.6l9.2,8.8l3.1,0.3l-0.7,1.2l6.1,4l1.4,4.1l-2.4,3.1l2.6-1.6l0.1-5.5l4,3.5 l2.4,7L95.2,348.97z", "name": "Guyane"},
        "63": {"path": "m449.1,332.4l3.5,4.3l2.5,2.7l-2.1,6.3l4.7,7.7 l4.6,3.8l2.7,7.5l-3,7.1l-11.1,0.7l-5.2-3.2l-8.5-0.1l-9.6,3.2v0.1l-3.7,4.1l-6.5-5.4l-6.7-0.3l-1.5-2.5l-7.5-3.5l-1.2-7.9l1.7-2.4 L401,349l-4.4-5.5l9.3-8.6l-2.3-6.7l0.5-4.1l2.5,1.2l11.2-8l2.3,1.7l-1.2,2.8l4.4,4.5l3.3-0.5l1.6,2.5l13.1,0.5l1.4,3.2L449.1,332.4z", "name": "Puy de Dôme"},
        "43": {"path": "m485.4,376.3l2.2,2.4l-0.9,4.1v3.1l-2.6-1.2 l-1.5,5.2l-2.6,2.3l0.7,2.8l-3.4,0.4l-3.4,5.9l-5.9,1.9l-6.7,6.4l-9-7.7l-2.9-0.2l-0.1,2.8l-6.4-1.2l-1.5-5.7l-2.3-1.7l-3.5-8 l3.4-0.2l-2.6-1.1l-3.3-8.7l-5.5-1.5v-3.9v-0.1l9.6-3.2l8.5,0.1l5.2,3.2l11.1-0.7l2.8-0.8l2.3,2l2.8-0.4l6.2-3l6,2.2l0.7,5.5 L485.4,376.3z", "name": "Haute Loire"},
        "42": {"path": "m499.3,365.9v4.4l-5,2.7l-3.2,4.9l-3.5,0.8 l-2.2-2.4l-2.6,1l-0.7-5.5l-6-2.2l-6.2,3l-2.8,0.4l-2.3-2l-2.8,0.8l3-7.1l-2.7-7.5l-4.6-3.8l-4.7-7.7l2.1-6.3l-2.5-2.7l5.5-4.8 l-1.5-12.4l0.5-3l3.9-1.3v3l5.2,3.3l8-1.5l2.1,2.1l5.7-3.8l0.01-0.09l2.09,2.99l-4.9,3.5l-1.6,8.6l5.2,6.7l-1.7,5.9l2.3,1.6 l-1.3,2.5l1.1,3l4.6,4.1l5.9,2.1l0.9,3l4.6,2.6h-0.1L499.3,365.9z", "name": "Loire"},
        "15": {"path": "m435.6,387.9l3.5,8l-1,1l-6.8,5.7l-1.7-2.3 l-6.9,15.9l-2.6-5.1l0.5-3l-2.4-1.6l-1.3-4.2l-5.2-4l-4.7,4.2l-3,9.1l-3.8,4.8l-6.4-1.5l-5.1,3.2l-3.3-5l1.7-5.8l-3.8-6l-1-5.4h0.1 l3.2-1.9l-1.5-3.4l3.1-1.1l0.3-3.5l2.3-1.9l-1.8-2.2l7.6-9.5l0.6-3.5l6.2,2l-0.7-6l7.5,3.5l1.5,2.5l6.7,0.3l6.5,5.4l3.7-4.1v3.9 l5.5,1.5l3.3,8.7l2.6,1.1L435.6,387.9z", "name": "Cantal"},
        "19": {"path": "m363.6,392.3l-8.1,0.8l-3.5-7l-3.2-0.7l-0.2-3 l-2.3-1.5l2-1.8l-1.7-3l3.6-4.6l-2.9-4.7l1.6-2.7l2.5,1.2l4.7-4l5.7-1.3l4.9-4.6l8.7-4l7-3.4l11.2,5.2l2.3-2.6l2.7,0.8l2.4-2.4 l1.2,5.6l-1.7,2.4l1.2,7.9l0.7,6l-6.2-2l-0.6,3.5l-7.6,9.5l1.8,2.2l-2.3,1.9l-0.3,3.5l-3.1,1.1l1.5,3.4l-3.2,1.9h-0.1l-6.7-0.2 l-5.3,2.7L363.6,392.3z", "name": "Corrèze"},
        "46": {"path": "m385.4,413.1l3.3,5h-0.1l-1.7,1.5L381,421 l-7.6,5l0.8,9.1l-6.2,0.8l-7.5,5.5l-2.6-2.3l-8.7,2.5l-0.5-4l-2.4,1.5l-2.7-1l-4.5-4l2.1-2.3l-3.1,0.5l-2.7-9.1l3.4-3.1l4.7-6.5 l6-3.4l-0.6-3.6l4.5-3.2l1.4-5.3l0.7-5l8.1-0.8l6.7,6.1l5.3-2.7l6.7,0.2l1,5.4l3.8,6L385.4,413.1z", "name": "Lot"},
        "71": {"path": "m517.2,270.2v0.1l0.4,2.9l8.3,4.4l-5,3.7 l2.5,1.6l2.2,8.7l-2.8,5l0.8,5.3l-2.8,1.1l-4.8-3.3l-5.4,1.3l-5.9-1.5l-5.9,20.9l-5.7-7.7l-1.6,2.3l-2.5-1.5l-2.2,1.6l-2.2-1.7 l-2.3,1.9l-0.29,2.91L482,318.2v0.1l-5.7,3.8l-2.1-2.1l-8,1.5l-5.2-3.3v-3l3.7-4.6l0.5-5.5l-1.6-2.4l-7.9-2.9l-6.7-13.5l7,2.8 l11.3-5.7l-0.7-2.9l1.9-2.1l-4-7l2.1-1.7l-0.3-5.9l2.8-1l2.7-1.7l1.1-0.3l2.4,2.6l5.7,1.7l1.3,2.6l5.5,1.9l5.6,6.3l8.8-4.2 L517.2,270.2z", "name": "Saône et Loire"},
        "26": {"path": "m535.1,404.4l-3,0.5l-0.8-17.5l-3,1.7l-8.2-1.9 l-2.7,1l1.1-6.3l-3.3-7.8l-4.9-2.7l-9,3.1l0.5,10.3l3.8,11.3l-1.5,6.2l-3.5,4.5l1,7.1l-3,5.9l-2.1,14.4l5.9,0.7l3.5,4.2l8.7-3.9 l2.4,1.4l2.5-2.2l0.5,5.8l9.3,0.9l0.1,2.8l5.2,2.3l4.3-4.8l2.3-0.1l1-0.2l0.2-4.7l-10-5.7l-1.5-2.6l3.2-5.1l4.2,1.4l2.5-2.5l-3-2.3 l2.5-6.7l5.8-0.3l0.3-3.4l-5.9-0.8L535.1,404.4z", "name": "Drôme"},
        "21": {"path": "m523.6,241.7l3.9,8.2l-1.2,1.3l-1.8,8.2 l-6.2,6.8l-1.1,4.1v-0.1l-15,1.5l-8.8,4.2l-5.6-6.3l-5.5-1.9l-1.3-2.6l-5.7-1.7l-2.4-2.6V260l0.4-3.2l-3.7-1.2l-1.3-6h0.1l-1.3-2.7 l1.3-8.1l6.7-10.4l-1.7-2.3l2.8-2.1l0.3-3.7l-3.1-3.9l1.9-3.1l2.2-2l6.1-0.9l4.7-3.9l3.9,0.5l3.5,0.7l0.5,2.7l2.6,1l-0.3,2.9 l2.9,0.3l1.8,2.2l1,3.1l-2.8,2.4l2.3,4.8l9.2,2l3,1.6v2.8l4.8-1.9h0.1l2.7-1.6l2,3l0.1,3.2l-4.6,4.1L523.6,241.7z", "name": "Côte d'Or"},
        "76": {"path": "m314.41,119.8l-7.61-1.8l-1.2-2l-0.1-2.3 l4.4-9.7l13.8-7.4L326,95l10.3-2.1l4.8-1.8l2.4,0.3L352,87l5.11-4.09l11.79,9.99l3.4,8.4l-3.1,4.7l1.4,8.7l-1.3,8l-13.1-3.3l-4.8,7 l-8.2,2.5l-1.4,2.7l-5.4-2.4l-2.1-2.1l1.9-2.4l-4.9-3l-3.4,0.7l-7.6-5L314.41,119.8z", "name": "Seine Maritime"},
        "38": {"path": "m513.6,349.4l-0.3-7.1l6,2l3-5.1l2.8-0.7 l11.2,15.2l6.5,10.5l6.2,0.2l0.3-2.8l9.4,2.1l2.7,6.3l-2.3,5.5l1,5.4l5.2,1.5l-1.6,3.8l1.8,4.2l4.4,3.1l-0.4,5.8l-3.1-1.1l-12.6,3.9 l-0.9,2.8l-5.5,1.2l-1,3.1l-5.9-0.8l-5.4-4l-3,0.5l-0.8-17.5l-3,1.7l-8.2-1.9l-2.7,1l1.1-6.3l-3.3-7.8l-4.9-2.7l-9,3.1l-2-4.2v-4.4 l-0.2-1.1h0.1l4.4-3.9l-1.9-2.5l2.5-2.5l6.9-1.5L513.6,349.4z", "name": "Isère"},
        "58": {"path": "m462.8,250l5.5-0.4l1.3,6l3.7,1.2l-0.4,3.2v0.8 l-1.1,0.3l-2.7,0.4v1.3l-2.8,1l0.3,5.9l-2.1,1.7l4,7l-1.9,2.1l0.7,2.9l-11.3,5.7l-7-2.8l-5.9,6l-4.4-3.7l-2.8,1.7l-6.4-0.2l-5.7-6.3 l1.3-8.9l0.2-5.8l-2.2-2l-0.3-5.9l-3.1-7.6l-2.7-2.1l1.7-5.8l-2-6.5l1.5-2.4l2.8-0.7v0.1h3.4l7.4,4.8h6l4.6-4.3l3.9,5.6l5.5,3 l5.8-0.9l0.9,3.7l2.8-0.9L462.8,250z", "name": "Nièvre"},
        "69": {"path": "m493.1,312.7l5.7,7.7l-1.5,8l3.1,9.2l2.8-0.5 l3.7,5.7l6.4-0.5l0.3,7.1l-2.5,5l-6.9,1.5l-2.5,2.5l1.9,2.5l-4.4,3.9l-4.6-2.6l-0.9-3l-5.9-2.1l-4.6-4.1l-1.1-3l1.3-2.5l-2.3-1.6 l1.7-5.9l-5.2-6.7l1.6-8.6l4.9-3.5l-2.09-2.99l0.29-2.91l2.3-1.9l2.2,1.7l2.2-1.6l2.5,1.5L493.1,312.7z", "name": "Rhône"},
        "91": {"path": "m401.6,164.8l2.3,2.2l0.5,1l-4,17.2L397,190 l-3.7-0.6l-2.8,1.8l-1.5-2.7l-1.9,2.9l-6.9,0.7l-2.8-10.6l4.6-8.6l-0.7-2.9l1.6-2.6l7.1-5.4v-0.1l3.7,1.6l5.1,2.1L401.6,164.8z", "name": "Essonne"},
        "45": {"path": "m393.3,189.4l3.7,0.6l0.7,3.1l4.2,4.3l-0.6,2.7 l-2.6,1.5l9.2,0.7l11.2-2.7l6.7,7.5l0.4,5.8l-4.6,4.9l1.1,2.9l-1.6,2.4l-5.3,3.3l3,2.8l2.2,6.9l-2.8,0.7l-1.5,2.4l-9,1.7l-3.9-4.8 l-13.5-3.1l-0.8-2.6l-15.2-1.6l-1.9,2.3l-2.9-0.6l-1.1-3.8l-2.4-1.5l-3,1.3l-1.7-5.6l1.8-3.2l-2.5-7.1l-0.2-2.8l6-2.8l8.5-0.6 l4.8-10.8l0.5-1.5l6.9-0.7l1.9-2.9l1.5,2.7L393.3,189.4z", "name": "Loiret"},
        "86": {"path": "m329.6,320.8v3.5l-4.8-0.6l-1.3,2.5l-3.8,1.4 l-4.1-3.9l-2.2,1.8l1.4,2.4l-2.9,0.8l-9.1-3l0.8-2.8l-4.5-3l3.2-8.5l-6.4-0.2l0.1-2.8l-2.1-2l-0.5-5.8l3.4-5.5l-1.5-3l-2.7,0.9 l4.5-14.1l-2.7-1.2l0.8-3l-3.4-9l6.6-5.9l5.5,3.2l0.3,3.2l2.9-0.3l1.3,6.1l2.8,1.4l10-0.4l-1.4-2.9l5.3,3l0.3,3.1l7.1,10l2.1,3 l-0.8,5.8l4.6,4.4h2.9l2.6,5.4l2.5,1.3l-1.5,2.8l-0.8-0.3l-1.3,2.4l-3.3-0.9l-1.3,3l-5.6,2.7L329.6,320.8z", "name": "Vienne"},
        "10": {"path": "m442.2,186.9l-3.6-1.5l-0.4-8.5l2.9-0.8l3-5 l3.2,4.5l9,1.2v-3.3l9.5-7.6l6.5-0.9l3.1,0.5l0.4,6.1l2.6,2c1.9,0.8,3.8,1.5,5.6,2.3l2.5-1.5l3.3,1.1l-0.6,3.4l2.4,5.2l5.6,3 l0.5,9.9l-0.1,2.7l-5.6,2.5l0.2,4.8l-3.9-0.5l-4.7,3.9l-6.1,0.9l-2.2,2l-2.9-1.4l-12.6,1.6l-5-10.9l-3.6-4.1l-2,2.2l-2.5-8.3 L442.2,186.9z", "name": "Aube"},
        "89": {"path": "m425.8,207.1l-6.7-7.5l3.9-5.4l0.2-5.8l15.4-3 l3.6,1.5l4.5,5.5l2.5,8.3l2-2.2l3.6,4.1l5,10.9l12.6-1.6l2.9,1.4l-1.9,3.1l3.1,3.9l-0.3,3.7l-2.8,2.1l1.7,2.3l-6.7,10.4l-1.3,8.1 l1.3,2.7h-0.1l-5.5,0.4l-1.5-2.8l-2.8,0.9l-0.9-3.7l-5.8,0.9l-5.5-3l-3.9-5.6l-4.6,4.3h-6l-7.4-4.8H421v-0.1l-2.2-6.9l-3-2.8 l5.3-3.3l1.6-2.4l-1.1-2.9l4.6-4.9L425.8,207.1z", "name": "Yonne"},
        "95": {"path": "m374.3,144l-9.5-0.8l4-9.5l1.6,3.2l5.6,1.1 l6.3-1.8l9.2,2.2l2.2-1.6l10.9,6.4l0.2,2l-1.7,2.3l-0.1,0.1c-1.5,1.1-3.1,2.2-4.7,3.3l-2.6-1.1l-3.3,1.3l-3.6,2.6l-5.5-6.1 L374.3,144z", "name": "Val d'Oise"},
        "37": {"path": "m303.9,263l-5.5-3.2v-0.1l5.8-15.3l1.7-9.3 l0.7-2.4l6.1,2.6l-0.5-3.3l2.8,0.3l7.7-4.5l10.5,0.5l-0.2,5.5l2.2-1.8l6,3.4l-0.7,2.7l3.4,5.1l-1.2,9.1l2.4,1.9l2.6-1.3l4.2,6.7 l1,4.9l-2.5,2.1l-2.6,3.5l-2.3-1.7l-5.1,2.7l-3.3,11.9l1,2.7l-5.6,0.5l-7.1-10l-0.3-3.1l-5.3-3l1.4,2.9l-10,0.4l-2.8-1.4l-1.3-6.1 l-2.9,0.3L303.9,263z", "name": "Indre et Loire"},
        "72": {"path": "m312.7,235.3l-6.1-2.6l-7.4-0.1l-5.2-3.3 l-3.1,1.3l-2-2l-0.2-3.5l-6.7,0.2l-0.6-2.7l-1.5-5.6l5.5-7.7l-1.4-3.4l5-4.6l-1.2-2.9l4.4-4l-0.3-6.9l4.3-2.7l3,0.1l11-5.9l2.8,1.5 l1.6,8.4l7.2,5l2.9-1l3.1,3.5l3.2,0.7l2.1,3.8l-0.4,1.8v0.1l-2,2.1l1.7,2.2l0.4,5.5l-6.3,10.6l-3.2,1.1l-0.6,3.5l-7.7,4.5l-2.8-0.3 L312.7,235.3z", "name": "Sarthe"},
        "78": {"path": "m364.1,158.1l-3.6-6.6l-1.8-5.8l2.3-2.6 l3.8,0.1l9.5,0.8l9,3.6l5.5,6.1l-2,3.1l3.2,5.2l-7.1,5.4l-1.6,2.6l0.7,2.9l-4.6,8.6l-3.1,0.7L372,180l-1.2-5.6l-6.2-5.4L364.1,158.1z", "name": "Yvelines"},
        "28": {"path": "m333.1,200.9l-2.1-3.8l-1.1-7.5l7.5-5.1 l-0.5-4.6l0.2-4.5l-4.8-4.4l-0.1-3.2l2.4-2.6l6-1.1l5.3-3.2l2.8,1.6l6-1.3l-0.2-2.8l6-6.9l3.6,6.6l0.5,10.9l6.2,5.4l1.2,5.6l2.3,2.2 l3.1-0.7l2.8,10.6l-0.5,1.5l-4.8,10.8l-8.5,0.6l-6,2.8l0.2,2.8l-3.3-1.9l-5.5,3.5L339,201.4l-6.3,1.3L333.1,200.9z", "name": "Eure-et-Loir"},
        "82": {"path": "m360,458.1l-0.5,2.8l-11.7,4.3l2.2,2.3 l-5.8,2.5l-1.7-2.3l-9.9,0.9l-2-6.9l-5.1-4.1l3.3-4.6l-5.3-1.7l3.6-4.7l2.8,0.2l-1.3-2.7l4.4-5.5l-2.4-1.4v-2.9l7.5-2l3.1-0.5 l-2.1,2.3l4.5,4l2.7,1l2.4-1.5l0.5,4l8.7-2.5l2.6,2.3l7.5-5.5l6.2-0.8l-0.4,3l3.1,2.7l-2.3,2.4l4.3,3.6l-9.2,3.2l-2.7-1.1l-3,1.1 l1.8,2.2l-1,2.7L360,458.1z", "name": "Tarn et Garonne"},
        "66": {"path": "m427.65,528.27l0.25,15.63l3.9,3.3l1.9,3.8 h-2.3l-8.1-2.7l-6.9,3.9l-3-0.2l-2.4,1.1l-0.6,2.4l-2.1,1.2l-2.4-0.7l-2.9,1l-4-3.1l-7-2.9l-2.5,1.4h-3l-1,2.1l-4.6,2l-1.9-1.7 l-1.7-4.8l-7.5-2l-2-2.1l2.02-2.31l7.98-2.39l1.3-2.6l7.4-0.2l8.6-3.9l-1.4-6l2.7-1.4l13.6,1l8.2-5.4L427.65,528.27z", "name": "Pyrénées Orientales"},
        "11": {"path": "m435.07,504.37l-1.47,1.53l-5.2,9.3l-0.9,3.5 l0.15,9.57l-9.45-5.57l-8.2,5.4l-13.6-1l-2.7,1.4l1.4,6l-8.6,3.9l-4.8-5.6l-3.3,1.2l-3.4-1.5l-0.8-5.7l3.2-0.6l1.2-3l-2.4-2.5 l2.2-2.3l-0.2-3.1l-2.2-5.2l-8.7-3.9l-2.2-5.5l8.4-10l1.4,2.7l5.2-1.8l0.5-0.8l1.8,2.3l6.3,0.9l1.1-3.3l2.8-0.5l12,1.4l-0.5,2.8 l3.5,5l2.5-1.6l1.4,2.9l3.1-0.8l3.8-5.3l1,2.9l13.8,4.7l1.7,2L435.07,504.37z", "name": "Aude"},
        "64": {"path": "m276.9,513.4l3.4-0.8l-0.4-2.9l8-9.3l-0.8-3.1 l2.7-1.4l-0.5-7.2h-2.9l1.5-2.8l-2.5-5.8l-6.6-0.3l-8.6,1.5l-3.3-1.1l-4.8,1.9l-2.2-2l-2.3,1.5l-2.5-2.3l-9.8,2l-1.6,2.2l-2.5-1.4 l-2.7,1.3l-1.2-2.8l-11,2.5l-3.98-1.89l-3.52,4.89l-2.7,1.9l-4.5,0.9l1.9,4.5l4.5-0.2l0.2,2.2l2.4,1l2.2-2.1l2.4,1.3l2.5,0.1 l1.4,2.8l-2.5,6.7l-2.1,2.2l1.3,2.2l4.3-0.1l0.7-3.4l2.3-0.1l-1.3,2.4l5.9,2.3l1.5,1.8h2.5l6.1,3.8l5.8,0.4l2.3-1l1.4,2.1l0.3,2.8 l2.7,1.3l3.9,4l2.1,0.9l1.1-2.1l2.7,2.1l3.6-1.1l0.19-0.16l1.41-9.34L276.9,513.4z", "name": "Pyrénées Atlantiques"},
        "65": {"path": "m314.7,524.1l-5.5,3.2l0.6,7l-0.7,0.2l-2.3-1.6 l-2.4,1.8l-2.5-0.5l-1.9-1.7l-3.9-0.3l-6.9,2.1l-2.2-0.9l-2.1-1.7l-1.1-2.5l-7.8-5.5l-2.11,1.84l1.41-9.34l1.6-2.8l3.4-0.8l-0.4-2.9 l8-9.3l-0.8-3.1l2.7-1.4l-0.5-7.2h-2.9l1.5-2.8l-2.5-5.8l3.9-0.5l2,3.5l3.3,1.3l2.6,8.7l5.5-0.6l1.4,3l12.4,2.2l-7.1,11.2l4.6,4 l-0.6,3.5l3.1-0.3l0.8,2.7L314.7,524.1z", "name": "Hautes Pyrénées"},
        "09": {"path": "m369.82,543.59l0.78-0.89l-2.6-1.1l-2-2.1 l-3.7-0.1l-1.7-1.7l-2.8,0.4l-1.3,2.1l-2.4-0.8l-2.8-5.9l-10-0.6l-1.3-2.8l-13.2-3.9l-0.5-1.4l3.8-5.2l2.8-1v-5.9l3.9-4l2.8-1.1 l6.2,4.1l-0.4-5.6l5.4-1.6l-3-4.8l2.8-1.1l3.4,5.5l2.8-0.5l0.6-2.8l5.7,2.2l2-2.3l2.2,5.5l8.7,3.9l2.2,5.2l0.2,3.1l-2.2,2.3l2.4,2.5 l-1.2,3l-3.2,0.6l0.8,5.7l3.4,1.5l3.3-1.2l4.8,5.6l-7.4,0.2l-1.3,2.6L369.82,543.59z", "name": "Ariège"},
        "34": {"path": "m474.1,481.6l-2.4-0.1l-5.9,2.6l-3.6,3.2 l-7.2,4.6l-4.3,4.2l2.1-3.5l-4.3,6.6h-6.8l-5.5,4l-1.13,1.17l-0.17-0.17l-1.7-2l-13.8-4.7l-1-2.9l-3.8,5.3l-3.1,0.8l-1.4-2.9 l-2.5,1.6l-3.5-5l0.5-2.8l3.4-2l0.8-3l-0.7-9.7l6.1,2.2c2.3-1.5,4.6-2.9,6.8-4.4l5.7,0.1l0.1-8.7l6.3,1.3l3-5.3l3.7-1l2.9,0.1 l0.4,2.9l2.2-1.7l5.3,2.5l0.1-2.8l4.2-3.9l3.9-0.6l1.9,2.6l-0.9,2.7l6.8,1.4l0.2,3l3,0.2l6.1,6.7l1.5,3L474.1,481.6z", "name": "Hérault"},
        "31": {"path": "m326.8,526.2l-5.5-1.5l-1.2,2.4l0.2,7.6 l-8.8-0.7l-1.7,0.3l-0.6-7l5.5-3.2l2.6-5.3l-0.8-2.7l-3.1,0.3l0.6-3.5l-4.6-4l7.1-11.2l3.1-1.1l3.5-5.3l11.4,2.5l0.7-5.8l6.5-6.1 l-9.1-13.3l9.9-0.9l1.7,2.3l5.8-2.5l-2.2-2.3l11.7-4.3l1.4,6.3l2.6,1.2l0.2,2.8l2.3,2.1l-0.7,5.4l14.3,9.3l1,2.8l-0.5,0.8l-5.2,1.8 l-1.4-2.7l-8.4,10l-2,2.3l-5.7-2.2l-0.6,2.8l-2.8,0.5l-3.4-5.5l-2.8,1.1l3,4.8l-5.4,1.6l0.4,5.6l-6.2-4.1l-2.8,1.1l-3.9,4v5.9 l-2.8,1l-3.8,5.2L326.8,526.2z", "name": "Haute Garonne"},
        "06": {"path": "m605.3,477.1l-3.2-0.1l-1.3,1.8l-0.1,2.2 l-0.42,0.77l-2.18-3.97l0.8-2.9l-5.6-2.6l-1.7-5.6l-5.5-2.9l3-1.4l-1.2-2.6l5.2-3.8l5.2,0.6l-7-7.7l-4.3-8.5l2-5.3l6.79-7.79 l6.91,7.79l6.9,1.6l4.2,2.8l2.5-0.4l1.8,1.4l10.3-2.4l2.7-1.8l-0.3,2.6l1.5,2.2l0.3,3.2l-1.6,1.9l-0.2,2.3l-2.7,1.6l-3.3,5l-0.5,1.6 l1.1,2.7l-1.1,2.7l-3.5,2.9l-2.3,0.5l-0.9,2.4l-3-0.9l-1.5,2.1l-2.3,0.5L609,472l0.1,2.8l-2.4,0.6L605.3,477.1z", "name": "Alpes Maritimes"},
        "2B": {"path": "m643.7,551.5v1l-3.2,1.7l-3.8-0.5l-3.7-11 l-2.7-1.1l-0.5-3.2l-6-5.9l-6-3.4l-1.2-3.5l-13.6-2.1v-0.2l3.9-5l-0.3-3.4l2.2-2.8l2.8-0.3l0.9-2.9l10.7-4.2l3.5-4.9l8.6,1.3 l-0.5-17.4l2.4-2l2.9,1.1l0.18,0.89l1.52,8.21l-0.5,10.6l4,5.6l3.8,26l-5.4,11.9V551.5L643.7,551.5z", "name": "Haute Corse"},
        "2A": {"path": "m640.5,554.2l3.2-1.7l0.7,8.4l-0.15,0.54 l-1.85,4.86l-2.7,1.9l3.3,0.4l-5.8,14.7l-3.1-1.2l-1.2-2.8l-11.2-3.4l-4.8-4.4l0.2-3l4.9-3.3l-9.5-1.9l2.7-7l-0.9-5.8l-7.3,2.6 l3-8.4l2.6-1.6l-7.9-4.4l-1.1-5.5l5.3-3.8l-3.8-4.2l-2.6,1l0.5-2.7l13.6,2.1l1.2,3.5l6,3.4l6,5.9l0.5,3.2l2.7,1.1l3.7,11 L640.5,554.2z", "name": "Corse du Sud"},
        "83": {"path": "m600.28,481.77l-1.38,2.53l-6.8,1.7l-0.7,2.5 l-5.5,5.7l5,0.7l-2,4.8l-4,0.2l-4.8,2.5l-3.5,1.1l0.1,2.7l-4.9-1.5l-2.7,0.5l-1.6,1.6l-0.4,2.3l-2.2,1.6l1.4-1.8l-2.4-1.7l-2.2,0.7 l-1.6-1.6l-3.1,0.1l0.9,2.2l-2.3-0.4l-1.5,1.7l-3-1.1l0.6-2.3l-6.4-4.1l-0.5-0.1l0.2-2.1l2.5-2l-2.2-6.3l1.1-2.6l2.7-0.5l-5.5-9.1 l2-5.3l3.3-0.8l-1.9-3.8l0.1-0.4l5.3,0.2l2.7-1.8l4,4.8l10.1-8.5l5.9,3.6l1.2-3.5l9.8-0.4l5.5,2.9l1.7,5.6l5.6,2.6l-0.8,2.9 L600.28,481.77z", "name": "Var"},
        "30": {"path": "m480,487.2l-2.8-0.6l-1.9-1.6l-1.1-3.4h-0.1 l3.3-4.4l-1.5-3l-6.1-6.7l-3-0.2l-0.2-3l-6.8-1.4l0.9-2.7l-1.9-2.6l-3.9,0.6l-4.2,3.9l-0.1,2.8l-5.3-2.5l-2.2,1.7l-0.4-2.9l-2.9-0.1 l-0.3-1.5l4.3-5.3l-8.4-4.1l2.8-1.1l2-3.9l7.8,3.4l3.9-0.5l0.1-3.3l8.7,2.2l6.3-1.8l-1.4-3l1.2-2.9l-3.9-7.7l3.6-2.5l1.1-2.1 l2.7,5.9l7.8,5l7.1-4.3l0.1,3.1l2.5-2.3h2.8l6,3.5l2.6,4.4l0.2,5.5l6.3,6.4l-4.5,5l-3.9,4.1l-1.9,10.6l-3.3-0.9l-4.2,4.8l1,2.7 l-5.8,1.8L480,487.2z", "name": "Gard"},
        "13": {"path": "m545,500.2l2.5-2l-2.2-6.3l1.1-2.6l2.7-0.5 l-5.5-9.1l2-5.3l3.3-0.8l-1.9-3.8l-0.1-0.1l-6.6,4.3l-3.2,0.2l-12-4.8l-3.5,0.7l-4.5-2.3l-5.5-5.7l-10.4-2.9l-3.9,4.1l-1.9,10.6 l-3.3-0.9l-4.2,4.8l1,2.7l-5.8,1.8l-3.1,4.9l0.2,0.1h13.2l2.2,0.9l1,2.2l-1.6,1.5l2.2,1.4l7.4,0.1l3.2,1.3l1.8-1.7l-1.5-2.8l0.4-2.4 l4.9,1l3,5.3l10-0.8l2.6-1.1l1.8,2l-0.2,2.5l1,2l-1.2,2.2h9.2l1.3,2l2.2-0.8l1.7,0.2L545,500.2z", "name": "Bouches-du-Rhône"},
        "40": {"path": "m222.32,481.21l1.08-1.51l3.9-7.1l8.8-37.8 l2-11.7v-0.4l5.8-2.6l3.7,1.3l-1.5,5.4l17.2-0.7l-0.4,2.8l11,7.7l0.6,4.7h6.4l1.6-3.6l2.1,2.7l0.4,4.6l11.7,2.9l-3.6,5.2l0.7,2.6 l-0.4,2.9l-2.5,1.3l-0.6-3l-9.4,2.7l0.5,6.4l-4.2,11.1l1.6,2.7l-8.6,1.5l-3.3-1.1l-4.8,1.9l-2.2-2l-2.3,1.5l-2.5-2.3l-9.8,2 l-1.6,2.2l-2.5-1.4l-2.7,1.3l-1.2-2.8l-11,2.5L222.32,481.21z", "name": "Landes"},
        "50": {"path": "m255.2,158.7l9.7,2.1l4.1,4.2l-1.8,6.7 l-3.6,4.5h-0.1l-8.6-0.8l-5.4-2.3l-7.1,4.8l-2.7-1l-4.7-9.6l1.9-0.2l4.8,0.4l2.5-1.1l0.5-2.2l-2.4,1.3l-5.1-5.6l-0.3-5.3l2-6.1 l-0.3-4.9l-1.8-3.6l0.4-7.4l1.5-2l-2.5,0.3l-2-5l0.3-2.2l-2.4-1.2l-2.9-4.1l-0.7-5.9l-1.4-1.9l1.8-1.8l0.1-2.8l-0.5-2.3l-2.2-1.1 l-1-2.5l2.1-0.2l11.9,4.2h2.4l4-2.6l5.1,0.6l1.8,1.7l0.9,2.7l-3.2,5.2l4,6.5l1.1,4.3l-0.1,0.7v0.1l0.5,6.6l8.4,3.1l-2,2.6l2.2,8.2 l-8.1,4.9l1.6,2.3l-4.5,5.3L255.2,158.7z", "name": "Manche"},
        "14": {"path": "m316.9,148l-0.7,2.2l-5.6-1l-7,1.7l-7.2,5.4 l-2.9,0.3l-5.7-1.1l-2.6,1.7l-4.9-3l-6.4,2.3l-2.7-1.3l-0.9,2.7l-5.4,2.9l-9.7-2.1l-1.8-2.4l4.5-5.3l-1.6-2.3l8.1-4.9l-2.2-8.2 l2-2.6l-8.4-3.1l-0.5-6.6v-0.1l0.1-0.7l1.8,0.8l1.9-2.1l3.4-0.3l9.4,3.3l13.9,1.5l6.9,3.4l5.7-0.7l4.7-2.5l4.1-3.7l5.1-1.1l0.3,8.3 h2.9l-2.3,2.1l2.8,9.4l-1.4,3L316.9,148z", "name": "Calvados"},
        "85": {"path": "m269.3,305.1l0.2-7.4l-4.7-17.9l-4.2-4.1l-2.3-5.7l-10.6-3.8l-4.8-3.5l-1.5,2.4l-3.2,0.7 l0.5,3l-2.4,2.1l-2.3-1.7v-3.1l-3.4,0.2l-0.2,9.5l-11.7-5l-5.6-5.6l-0.3,0.1l-0.8,2.6l-3.4,4.3l-1.2,2.3l0.2,2.4l8.7,9.5l2.7,5.6 l1.2,5.3l8,5.4l3.4,0.5l3.9,4.3l2.9-0.1l2,1.2l1.8,2.5l-0.9-2.1l3.9,3.3l0.5-2.7l2.4,0.3l7.1-2.7l-1.4,2.9l6.5-0.3l2.4,1.8l9.1-4.5 L269.3,305.1z", "name": "Vendée"},
        "17": {"path": "M242.8,341.1l-1.4-5l-3.5-3l-1.3-2.3l1.5-3.6l1.7,1.8l2.9,0.5l1.4,8.4L242.8,341.1z M241.9,318.9l-5.8-4.5 l-4.4-1.5l-0.6,2.9l2.7,0.1l4.8,3.3L241.9,318.9z M286.5,374.8l-6-1.2l1.7-3l-2.3-2l2.4-1.7l-1.5-2.6l1.7-2.6l-2.4-1.7l-0.3-3 l-5-3.1l2.2-2.1l-3.2-5.6l8.1-3.3l2.3,2l2.7-0.1l2.7-11.6l-3.6-4.7l-17.4-6.7l-5.9-6.5v-3.7l-2.4-1.8l-6.5,0.3l1.4-2.9l-7.1,2.7 l0.5,0.1l-0.6,3.4l-4.5,5.9l2.4,0.3l2.2,1.7l3,7.2l-1.5,1.9l-0.2,5.1l-3.3,3.1l-0.1,2.6l-2.2,0.4l-1.5,1.7l1.1,4.3l9,6.5l1.5,2.6 l4.3,2.7l3.7,4.8l1.81,7.3l3.79-0.5l0.7,2.8l6.4,1.7l0.6,5.8l6.1,4.3l9.4,1l2-5l0.1-0.4v-0.1L286.5,374.8z", "name": "Charente Maritime"},
        "33": {"path": "m243.9,420.1l-5.8,2.6v-4.6l2.2-3.2l0.5-2.3 l1.9-1.7l1.8,1.4l3.1-0.2l-1.1-4.6l-3.5-3.4l-2.8,4l-1.2,3.8l6.2-50l0.9-2.8l3.3-3.4l1.4,4.7l9,9l2.8,7.6l1.7-3.1l-0.59-2.4 l3.79-0.5l0.7,2.8l6.4,1.7l0.6,5.8l6.1,4.3l9.4,1l3.7-0.6l1.2,2.8l-2.8,5.5l-0.2,5.7l-2,2l9.8,2.3l2-3.2l2.4,2.6l-0.9,2.9l1,1.3 l-3.1-0.1l-1.2,2.5l-2.7-0.9l-1.1,3.3l2.9,1.4l-8.5,8.6l-0.6,8.9l-3,2.3l1.5,2.5l-4.5,4l-2.1-2.7l-1.6,3.6h-6.4l-0.6-4.7l-11-7.7 l0.4-2.8l-17.2,0.7l1.5-5.4L243.9,420.1z", "name": "Gironde"},
        "44": {"path": "m213.1,265.2l1.8-1l-2.8-4.1l-7.8-3l3-1.3 l0.6-2.2l-0.5-2.5l1.4-2.1l5.8-1.1l-5.5-0.7l-6.6,3.7l-4.1-3.2l-2.2,1l-2.2-1.2l-0.5-4.9l0.9-2.5l3-0.5l-0.9-2.2l-0.18-0.31 l13.18-3.89l0.4-6l5.2-3.4l13.2-0.4l1.6-2.9l9-3.9l6.8,3.6l7.2,13.3l-2.7-0.4l-1.9,2.4l8.5,3.3l0.3,5.9l-14.3,2.1l-2.9,2.2l3,0.8 l3.6,4.7l0.8,2.8l-2.8,4.5l2.8,1.4l0.4,3l-4.8-3.5l-1.5,2.4l-3.2,0.7l0.5,3l-2.4,2.1l-2.3-1.7v-3.1l-3.4,0.2l-0.2,9.5l-11.7-5 L213.1,265.2z", "name": "Loire Atlantique"},
        "22": {"path": "m208.7,188.9l-4.9,7.1l-2.9,1.1l-1.5-2.7 l-3.5-0.9l-6.2,7.5l-1.8-6l-3,0.9l-12.9-6.5l-7.9,3l-12.46-3.29l2.06-4.11l-2.5-9.3l2.5-8.3l-3.6-4.7l1.1-4.3l1.2,1.4l3.2-0.4 l1.1-7.7l1.5-1.6l2.2-0.6l1.9,1.4h2.5l2.1-1l2.2,0.3l1.5-1.8l0.9,2L170,153l3-3.6l2.9-0.8l-0.1,2.3l-1.2,4.4l1.7-3.1l2.6-0.5l-1.1,2 l7.2,7.8l2.2,5.4l3,2l0.8,3.7l0.7-2.2l3-1l2.4-2.7l8.1-3.3l2.7-0.2l-2,2.5l2.9-1.1l1.8,4.4l1.3-1.9l2.5,0.2v-0.09l1.6,3.99h-0.3h0.3 l2.5,0.3l0.7,0.2l0.4,1.7l-1.9,13L208.7,188.9z", "name": "Côtes d'Armor"},
        "35": {"path": "m255.2,207.2l-5.5,2.2l-3.6,8.6l-0.4,2.2 l-6.8-3.6l-9,3.9l-1.6,2.9l-13.2,0.4l-5.2,3.4l-1-5.8l3-0.7l-2.8-1.5l2.4-2.2l1-3.2l-2.4-1.7l1.6-2.6l-1.2-2.5l-5.1-2.8l-0.5-2.8 l3.5-0.9l-3.6-0.1l-1-4.4l4.9-7.1l9-2.5l1.9-13l-0.4-1.7l-0.7-0.2l-2.5-0.3l-1.6-3.99l0.05-0.86l0.05-0.85l0.7-0.1h2.1v0.1l1.7,4.4 l1.3,2l-0.5,2.1l1.4-2.1l-2.3-5.1l0.7-2.5l2.2-1.5l2.3-0.6l2.2,1l-1.5,2.3l2.9,2.4l7.3-0.6l4.7,9.6l2.7,1l7.1-4.8l5.4,2.3l-0.1,12.1 l-1.5,2.4L255.2,207.2z", "name": "Ille et Vilaine"},
        "29": {"path": "m151.6,210.1l2,3.4l-0.8,1.4l-5.5-1.2l-1.2-1.9 l2.2-0.7l-3,0.8l-0.3-2.7v2.7l-2.5,0.7l-2.2-1l-4.2-6.1l-0.8,2.5l-2.3,0.2l-3.5-3.1l1.6-4.6l-2.4,4.3l1.3,1.9l-2.2,1l-1,2.8 l-5.9-0.2l-2.1-1.6l1.5-1.6l-1.5-5.5l-2.4-3.1l-2.8-1.8l1.6-1.7l-2.1,1.4l-7.5-2.2l2.2-1.3l12.5-1.8l1.8,1.8l2-1.3l0.7-2.5l-1.6-3.6 l-6.8-2.5l-1.5,2.6l-2.6-4.2l1.3-1.8l-0.3-2.2l1.7,2.3l4.9,1l4.6-0.8l2.1,3.1l5.4,1l-3.7-0.9l-2.8-2l2.2-0.5l-4.2-2l2-1.5l-2.6-0.2 l-2.7,0.8l-0.8-2.2l7.1-4.5l-4.4,2.2l-2.3,0.1l-7.5,2.9l-2.7-1.2l-2.7,1.2l-1.5-1.8l0.6-5.3l2.5-1.6l-2.2-0.9l0.8-2.6l1.8-1.6 l2.1-0.8l5.1,1.5l-1.9-1.1l2.5-1.2l1.6,1.4l-1.9-1.7l1.2-1.9l2.9-0.1l3.8-2l2.3,2.6l6.7-3.1l3,1.6l1-2.2l2.9-0.5l0.4,5l2.2-1.5 l1.3,2.5l1.2-4.5l4.7,0.3l1.2,1.7l-1.1,4.3l3.6,4.7l-2.5,8.3l2.5,9.3l-2.06,4.11l-0.04-0.01v0.1l-6.8,3.2l0.5,3.5l3.4,5.5l8.1,1.3 l0.1,5.4l-2.5,2.8L151.6,210.1z", "name": "Finistère"},
        "56": {"path": "M167.7,242.6l2.9,1.2l-1.1,2.1l-5.1-1.2l-1.3-2.7l0.4-3l2.1,1.4L167.7,242.6z M209.1,219.2l2.4-2.2l1-3.2 l-2.4-1.7l1.6-2.6l-1.2-2.5l-5.1-2.8l-0.5-2.8l3.5-0.9l-3.6-0.1l-1-4.4l-2.9,1.1l-1.5-2.7l-3.5-0.9l-6.2,7.5l-1.8-6l-3,0.9 l-12.9-6.5l-7.9,3l-12.46-3.29l-0.04,0.09l-6.8,3.2l0.5,3.5l3.4,5.5l8.1,1.3l0.1,5.4l-2.5,2.8l-2.8-0.8l2,3.4l0.1,1.5l2.9,4.4 l2.3-0.2l1.5-1.7l-0.8-5.1l0.6,2.4l1.7,1.7l1.9-1.7l-2.5,4.2l2.2,1.4l-2.3-0.6l3.2,1.9l0.1,0.1l1.6,1l1.7-2.5l-1.6,3.1l2.1,2.6 l0.6,3.5l-0.9,2.8l2.1,1.1l-1.2-3l0.5-3.8l2.2,1.6l5.1,0.1l-0.7-5l1.4,2l2.1,1.5l4.8-0.5l2.1,2.4l-1,2.2l-2.1-0.6l-4.8,0.4l3.8,3.3 l12.9-0.9l3.1,1.5l-3.4,0.1l1.42,2.39l13.18-3.89l0.4-6l-1-5.8l3-0.7L209.1,219.2z", "name": "Morbihan"},
        "93": {"path": "M404.7,152.7l-1.3,2.5l1.3,4.7v0.1l-7.1-2.6l-0.8-2.7l-3.2-0.5l0.1-1.1l-1.3-2l3.3-1.3l2.6,1.1 c1.6-1.1,3.2-2.2,4.7-3.3L404.7,152.7z M663.2,73.89l0.06-0.08l-0.02-0.04l2.61-3.38l-3.95-0.3l-1.6-5.9l0.06-0.06l-0.02-0.06 l6.36-6.56l0.1-5.42l1.1-4l-1.2-3.4l-5.1-8l0.07-0.08l-0.03-0.04l2.65-3.33l-0.89-4.04l-4.5-2.9l-4.1,1.7l-6.4,8.8l-8.2,6.2 l-0.7-0.2l-7.8-1.1l-1.9,1l-5.1-4.6l-1.3-0.2l-1.9-0.7l-5.1,3l-1.6,2.7l-1-1.2l-5.9-2.1l-1.96,2.25v0.2l0.66,2.45l3.9,0.8l4.7,1.9 l0.1,1.4l0.1,1.1l-0.2,0.9l-0.3,0.9l-0.7,1.9l-1.6,0.7l-0.3,0.8l-1.4,0.7l0.6,2l0.7,2l13.9-0.2l0.1,0.1l1.8,3.6l1.8,2.4l0.6,0.8 l0.1,0.5L631,68l0.4,5.4l0.4,1.8l5.9-0.5l0.5-0.3c0.1,0,0.1,0,0.2,0l6.3-2.8l2.9,0.4l0.7,1.3l3,1.5l4,2.9c0,0.1,0.1,0.2,0.2,0.2 l0.7,0.5l6,6.2l0.8,0.6c0.1,0,0.2,0.1,0.3,0.1l3.6,2.6l0.04-0.13l0.43-1.3l0.23-0.68l-1.8-6L663.2,73.89z", "name": "Seine St Denis"},
        "75": {"path": "M641.8,78.3l-0.2,3.8l-1,2.6l-8.3-1.7l-6-0.6l-5.2,3h-4l-2.5-0.3l-0.4-0.1l-13.5-5l-3-3.8l-4.3-1.9l-0.5-0.2 l0.4-1.9l1.3-3.1l2.7-2.1l2.9-1.1l3.9,0.5h0.1l0.9-2.2l7.1-4.6l14-0.1l1.8,3.6l1.8,2.4l0.6,0.9l0.1,0.4L631,68l0.4,5.4l0.4,1.8v0.1 l-0.3,0.8l0.1,3.6l0.6-0.5l1.6-1.6l2-0.5l2-0.5L641.8,78.3z M396.8,154.7l-3.2-0.5l-2.5,1.7l3,3.5l5.3-0.1l-1.8-1.9L396.8,154.7z", "name": "Paris"},
        "94": {"path": "M404.7,160l0.3,2.9l-1.1,4.1l-2.3-2.2l-2.8,0.8l-5.1-2.1l0.4-4.1l5.3-0.1l-1.8-1.9L404.7,160z M668.09,102.2 h0.06l-0.02-0.12l3.31-0.19l-1.55-3.58l-3.69-2.41l0.8-8h-0.1l-3.6-2.6c-0.1,0-0.2-0.1-0.3-0.1l-0.8-0.6l-6-6.2l-0.7-0.5 c-0.1,0-0.2-0.1-0.2-0.2l-4-2.9l-3-1.5l-0.7-1.3l-2.9-0.4l-6.3,2.8c-0.1,0-0.1,0-0.2,0l-0.5,0.3l-5.9,0.5v0.1l-0.3,0.8l0.1,3.6 l0.6-0.5l1.6-1.7l2-0.4l2-0.5l4,1.7l-0.2,3.8l-1,2.6l-8.3-1.7l-6-0.6l-5.2,3h-4l-2.5-0.3l-0.6,1.1h-0.1l-0.9,0.5l-0.5,0.3l-0.5,0.3 l1,2.5v0.1l-0.8,2.8l-0.3,1.2l1.4,1.9l-0.7,2.1l-0.4,1.3l-0.7,1.2l0.78,5.38h0.06l2.1,0.2l4.7,2.8l3.1-2.2l0.1,5.5l3.3,2.4l4.9-1.8 l0.7,2.5l5.2-2.3l0.5,1.3l1.7,1.7l4.6-3.6l2.1-0.5l5.2-1.8l1.9,6.8l1.7,2.5l3.3,1.8l5.44,1.88l-0.68-5.05l0.05-0.08l-0.01-0.04 l2.5-4.2l2.73-2.74l-1.38-3.64l0.07-0.06l-0.03-0.07l2.35-1.96L668.09,102.2z", "name": "Val de Marne"},
        "92": {"path": "M391.1,155.9l3,3.5l-0.4,4.1l-3.7-1.6v0.1l-3.2-5.2l2-3.1l3.6-2.6l1.3,2l-0.1,1.1L391.1,155.9z M612.6,54.1 l1.6-0.7l0.7-1.9l0.5-1.8l-0.1-1.1l-0.2-1.4l-4.6-1.9l-4.6-0.9l-4,1.3l-7.6,5.6l-6.1,5.8l-5.3,3l-1,1l-3.75,7.4l1.79,7.17 l-0.06,0.07l0.01,0.06l-2.74,3.23l0.68,2.44l2.5,4.8l3.3-0.5l1,5.2l3.9-0.3l1.4,3.5l3.4,1.6l0.5,2.1l5.3,4.2l4.3,1.3l-0.1,4.9 l5.7,3.5l3.15-5.91l-0.7-5.46l0.72-1.2l0.4-1.3l0.7-2.1l-1.4-1.9l0.3-1.2l0.8-2.8l-1-2.6l0.5-0.3l0.5-0.3l0.9-0.5l0.7-1.1l-0.4-0.1 l-13.5-5l-3-3.8l-4.3-1.9l-0.5-0.2l0.3-1.9l1.4-3.1l2.7-2.1l2.8-1.1h0.1l3.9,0.5l0.9-2.2l7.2-4.6l-0.7-2l-0.6-2l1.4-0.7L612.6,54.1z", "name": "Hauts de Seine"}
    }
});
var couleurs = {"80": "#5BC6ED", "02": "#5BC4ED", "77": "#97C00D", "12": "#94F900", "47": "#FCED0D", "81": "#94FE00", "07": "#F19DAC", "90": "#A8CEEF", "48": "#A3A9B3", "84": "#3878FF", "01": "#F19CAC", "73": "#F1A2AC", "74": "#F1A3AC", "39": "#A8CCEF", "25": "#A8CBEF", "04": "#3873FF", "05": "#3874FF", "32": "#94FB00", "24": "#FCEA0D", "51": "#E09E63", "60": "#5BC5ED", "52": "#E09F63", "88": "#65B661", "27": "#E6C099", "61": "#E66066", "79": "#D58AB5", "16": "#D588B5", "49": "#EBFC94", "53": "#EBFD94", "59": "#F8C434", "62": "#F8C534", "08": "#E09C63", "54": "#65B361", "57": "#65B561", "67": "#CCB32F", "68": "#CCB42F", "55": "#65B461", "87": "#FE5CE6", "18": "#ABD6C3", "03": "#F4AE9F", "41": "#ABDAC3", "70": "#A8CDEF", "23": "#FE5BE6", "36": "#ABD8C3", "971": "#FA8630", "972": "#FA8730", "974": "#FA8930", "973": "#FA8830", "63": "#F4B19F", "43": "#F4B09F", "42": "#F1A0AC", "15": "#F4AF9F", "19": "#FE5AE6", "46": "#94FC00", "71": "#FBF283", "26": "#F19EAC", "21": "#FBF083", "76": "#E6C199", "38": "#F19FAC", "58": "#FBF183", "69": "#F1A1AC", "91": "#97C20D", "45": "#ABDBC3", "86": "#D58BB5", "10": "#E09D63", "89": "#FBF383", "95": "#97C60D", "37": "#ABD9C3", "72": "#EBFE94", "78": "#97C10D", "28": "#ABD7C3", "82": "#94FF00", "66": "#A3AAB3", "11": "#A3A6B3", "64": "#FCEE0D", "65": "#94FD00", "09": "#94F800", "34": "#A3A8B3", "31": "#94FA00", "06": "#3875FF", "2B": "#FD9264", "2A": "#FD9164", "83": "#3877FF", "30": "#A3A7B3", "13": "#3876FF", "40": "#FCEC0D", "50": "#E65F66", "14": "#E66166", "85": "#EBFF94", "17": "#D589B5", "33": "#FCEB0D", "44": "#EBFB94", "22": "#64A0D3", "35": "#64A2D3", "29": "#64A1D3", "56": "#64A3D3", "93": "#97C40D", "75": "#97BF0D", "94": "#97C50D", "92": "#97C30D"};
$(document).ready(function() {
    var url = $("#panel_map").data('url');
    findColors(url);

    $("a[aria-controls='players']").on('click', function(){
        $('svg').attr('width', $('#panel_map').width());
        var scale = $('#panel_map').width()/735;
        scale = (scale < 1)?1:scale;
        $('svg g').attr('transform', 'scale(' + scale + ') translate(' + scale + ', ' + 0 + ')');
    });

    $("a[aria-controls='clubs']").on('click', function(){
        $('svg').attr('width', $('#panel_map').width());
        var scale = $('#panel_map').width()/735;
        scale = (scale < 1)?1:scale;
        $('svg g').attr('transform', 'scale(' + scale + ') translate(' + scale + ', ' + 0 + ')');

    });

    var countPlayer = [];
    
    function findColors(url){

        $.ajax({
            type: "POST",
            url: url,
            data: {
                type: 'map'
            },
            success: function (response) {
               colorMapPlayers(response.players.color);
               colorMapClubs(response.clubs.color);
                countPlayer = response.players.count;
            }
        })
    }

    function colorMapPlayers(colors){
        $('#francemap_players').vectorMap({
            map: 'france_fr',
            hoverOpacity: 0.5,
            hoverColor: false,
            backgroundColor: "#ffffff",
            colors: colors,
            borderColor: "#000000",
            enableZoom: false,
            showTooltip: true,
            onRegionClick: function(element, code, region)
            {
                $('#modal_players_label').html("Players "+region+" - "+code);

                var count = 0;
                if(countPlayer[code] != undefined){
                    count = countPlayer[code];
                }

                $('#modal_players_content').html('<p style="color: #00acd6; font-size: 20px"><i class="glyphicon glyphicon-user" ></i> '+ count +'</p>');
                $('#modal_players').modal('toggle');
            }
        });
    }

    /**
     *
     * @param colors
     */
    function colorMapClubs(colors){
        $('#francemap_clubs').vectorMap({
            map: 'france_fr',
            hoverOpacity: 0.5,
            hoverColor: false,
            backgroundColor: "#ffffff",
            colors: colors,
            borderColor: "#000000",
            selectedColor: "#EC0000",
            enableZoom: false,
            showTooltip: true,
            onRegionClick: function(element, code, region)
            {
                $('#modal_clubs_label').html("Clubs "+region+" - "+code);
                getClubByRegion(code);
                $('#modal_clubs').modal('toggle');
            }
        });
    }

    /**
     *
     * @param regionId
     */
    function getClubByRegion(regionId){
        $.ajax({
            type: "POST",
            url: $('#modal_clubs').data('url'),
            data: {
                regionId: regionId
            },
            success: function (response) {
             displayClub(response);
            }
        })
    }

    function displayClub(clubs){
        $(".link_club").remove();
        $('#modal_clubs_content').append('<div class="row">');
        $(clubs).each(function( key, club ) {
            $('#modal_clubs_content').append('<div class="col-xs-4"><a class="link_club" href="'+club.url+'">'+club.title+'</a></div>');
        });
        $('#modal_clubs_content').append('<div class="clearfix"></div>');
        $('#modal_clubs_content').append('</div>');

    }

});
