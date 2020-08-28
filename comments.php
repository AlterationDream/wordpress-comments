function comments_callback($comment, $args, $depth) {
        ?>

        <li>
            <article class="comment">
                <div class="comment-inner">
                    <header class="comment-header row">
                        <div class="col">
                            <cite class="comment-author"><?php echo $comment->comment_author; ?></cite>
                            <time class="comment-datetime" datetime="get_comment_date('Y-m-d')"><?php printf('%1$s в %2$s', get_comment_date('j F, Y'), get_comment_time()) ?>
                            </time>
                        </div>
                    </header>
                    <div class="comment-body">
                        <p><?php comment_text(); ?></p>
                    </div>
                </div>
            </article>


        <?php
    }

    function display_comments_form($post_id) {
        ?>

        <section class="section-comment">
            <div class="container">
                <div class="text-center d-inline-block title-comments-block">
                    <h2 class="ui-title">Комментарии</h2>
                </div>
                <ul class="comments-list list-unstyled">


                <?php
                $commentsPP = get_option('comments_per_page');
                $comments = get_comments(array(
                    'post_id' => $post_id,
                    'status' => 'approve'
                ));
                wp_list_comments(array(
                    'per_page' => $commentsPP,
                    'reverse_top_level' => false,
                    'style' => 'li',
                    'short_ping' => 'true',
                    'callback' => 'comments_callback',
                ), $comments);

                $cpage = ceil(get_comments_number($post_id) / $commentsPP);

                if( $cpage > 1 ) {
                    echo '<div class="misha_comment_loadmore">Ещё комментарии</div>
                    <script>
                    var ajaxurl = \'' . site_url('wp-admin/admin-ajax.php') . '\',
                        parent_post_id = ' . $post_id . ',
                        spage = 1,
                        cpage = ' . $cpage . '
                    </script>';
                }

                if (!$comments) echo '<h4 style="font-weight:400; text-align: center">Будьте первым, кто оставит комментарий.</h4>';
                ?>

                </ul>
            </div>
        </section>

        <section class="section-reply-form" id="section-reply-form">
            <div class="container">

                <?php
                $fields = array(
                    'author' => '<div class="col-md-5">
                                <div class="form-group unique">
                                    <label class="form-label">Ваше имя*</label>
                                    <input class="form-control unique" name="author" type="text" required />
                                </div>',
                    'email' => '<div class="form-group unique">
                                    <label class="form-label">Email*</label>
                                    <input class="form-control unique" name="email" type="email" required />
                                </div>
                            </div>
                            </div>'
                );
                $args = array(
                    'id_form' => '',
                    'class_form' => 'form-reply ui-form',
                    'class_submit' => 'btn btn-secondary w-100',
                    'name_submit' => 'Оставить комментарий',
                    'title_reply' => '<div class="text-center d-inline-block">
                                        <h2 class="ui-title">Оставить комментарий</h2>
                                    </div>',
                    'title_reply_to'    => 'Ответить %s',
                    'cancel_reply_link' => 'Закрыть ответ',
                    'label_submit' => 'Отправить',
                    'must_log_in' => '',
                    'logged_is_as' => '',
                    'comment_notes_before' => '',
                    'comment_field' => '<div class="row"><div class="col-md-7">
                                            <div class="form-group unique">
                                            <label class="form-label">Сообщение*</label>
                                            <textarea class="form-control unique" name="comment" rows="6" required></textarea>
                                        </div>
                                        </div>',
                    'fields' => apply_filters( 'comment_form_default_fields', $fields ),
                    'comment_notes_after' => ''
                );
                comment_form($args, $post_id);?>

            </div>
        </section>
        <script>
            $("#wp-comment-cookies-consent").prop( "disabled", true );
            $(".form-reply.ui-form .form-control:not(.unique)").prop( "disabled", true );
            $(".form-reply.ui-form #phone").prop("required", false);
        </script>

        <?php
    }

    function comments_loadmore() {
        wp_enqueue_script( 'comments-loadmore', get_template_directory_uri() . '/assets/js/comments.loadmore.js', array('jquery') );

    }
    add_action( 'wp_enqueue_scripts', 'comments_loadmore', 1 );

    function comments_loadmore_handler(){

        // actually we must copy the params from wp_list_comments() used in our theme

        $comments = get_comments(array(
            'post_id' => $_POST['post_id'],
            'status' => 'approve'
        ));
        wp_list_comments(array(
            'per_page' => get_option('comments_per_page'),
            'reverse_top_level' => false,
            'page' => $_POST['spage'],
            'style' => 'div',
            'short_ping' => 'true',
            'callback' => 'comments_callback',
        ), $comments);

        die; // don't forget this thing if you don't want "0" to be displayed
    }
    add_action('wp_ajax_cloadmore', 'comments_loadmore_handler'); // wp_ajax_{action}
    add_action('wp_ajax_nopriv_cloadmore', 'comments_loadmore_handler'); // wp_ajax_nopriv_{action}
