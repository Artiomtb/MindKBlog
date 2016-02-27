<?php

use Framework\DI\Service;
use Framework\Response\Response;

$include = function ($class_name, $method_name, $params) {
    $response = new Response("");
    if (class_exists($class_name)) {
        $controller = new $class_name;
        $response = call_user_func_array(array($controller, $method_name . "Action"), $params);
    }
    echo $response->getContent();
}

?>

<div class="col-sm-8 blog-main">
    <?php foreach ($posts as $post) { ?>

        <div class="blog-post">
            <h2 class="blog-post-title"><a href="/posts/<?php echo $post->id ?>"> <?php echo $post->title ?></a></h2>

            <p class="blog-post-meta"><?php echo date('F j, Y', strtotime($post->date)) ?>
<!--                by <a-->
<!--                    href="#">--><?php //echo $post->name ?><!--</a>-->
            </p>

            <?php echo htmlspecialchars_decode($post->content) ?>
        </div>

    <?php } ?>

    <div>
        <?php $include('Blog\\Controller\\PostController', 'getPost', array('id' => 'TestId')) ?>
    </div>

</div>

