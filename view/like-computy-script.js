jQuery(document).ready(function($){


    $('.emoji-item').click(function(e) {
      //  alert('golos');
        $(this).addClass('cliick');
        $('.active').removeClass('active');
        $(this).addClass('active');
        $('.like_computy').addClass('blednost');
        e.preventDefault();
        let znach = $(this).children(".lc-value");
        let oldsimble = znach.text();

        let sesid = $('.sesid').val();
        let postid =  $('.postid').val();
        let voteid = $(this).attr('data-type');

        //проверка есть ли индификатор сессии для этой записи
        $.ajax({
            type: "POST",
            url: window.wp_data.ajax_url,
            data: {
                action : 'get_like_computy_value',
                sesid: sesid, postid: postid,voteid:voteid

                //вставить данные о сессии и о id поста
            },
            success: function (response) {
               // console.log(response);
                $('.cliick').removeClass('cliick');
                $('.like_computy').removeClass('blednost');

                if(response == 'voteadd0'){

                    //голос добавлен
                    let newsible = parseFloat(oldsimble)+1;
                    znach.text(newsible);

                }
                if(response == 'etotgolos0'){

                    alert('Этот голос уже выбран');
                }

                if(response == 'drugoygolos0'){

                    let newsible = parseFloat(oldsimble)+1;
                    znach.text(newsible);

                    let vashvote = $('.vashvote').val();

                    let vashvoteold= $('.emoji-item[data-type="' + vashvote + '"]').children(".lc-value");
                    let vashvoteoldsimble =vashvoteold.text();
                    let vashvotenewsimble = parseFloat(vashvoteoldsimble)-1;
                    vashvoteold.text(vashvotenewsimble);
                    $('.vashvote').val(voteid);
                }

            }
        });
            });
});
