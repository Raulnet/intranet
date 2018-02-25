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
