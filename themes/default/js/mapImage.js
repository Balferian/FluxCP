    $(document).ready(function(){
        $('.hide_shop').on('click', function(){
            id = $(this).attr('data');
            $('.shop_' + id).toggleClass('map_hide');
        });
        $('.show_shop').on('click', function(){
            id = $(this).attr('data');
            $('#load_shop_' + id).show();
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: '/?module=map&action=view&npc_id=' + id,
                success: function(data){
                    $('.shop_' + id).toggleClass('map_hide');
                    $('#load_shop_' + id).hide();
                    if(!data.length){
                        $('#shop_' + id).text('Not found items in this shop.');
                    } else{
                        var table = '<table class="vertical-table"><tr><th>Image</th><th>Name</th><th>Price</th></tr>';
                        for(var i in data){
                            var item = data[i];
                            table += '<tr>';
                            if(item.img) {
                                table += '<td><img src="' + item.img + '" /></td>';
                            } else {
                                table += '<td></td>';
                            }
                            if(item.link){
                                table += '<td><a href="' + item.link + '">' + item.name + '</a></td>';
                            } else {
                                table += '<td>' + item.name + '</td>';
                            }
                            table += '<td>' + item.price + '</td>';
                        }
                        $('#shop_' + id).html(table);
                    }
                    console.log(data);
                },
                error: function(){
                    $('#load_shop_' + id).hide();
                    alert('Error with loading items');
                }
            })
        });
        $('.npcs_hover').hover(function(){
            $('.' + $(this).attr('data')).show();
        }, function(){
            $('.' + $(this).attr('data')).hide();
        });
        $('.tab').on('click', function(){
            $('.tab').each(function(){
                $('#' + $(this).attr('data')).hide();
                $(this).removeClass('active');
            })
            $('#' + $(this).attr('data')).show();
            $(this).addClass('active');
        });
    })
