<?php
if(!defined("IS_CORE")) {
	echo "403 ERROR";
	die();
}

$cardinalCache = array();

require_once(PATH_CORE."functions.php");

$tplSite = "index";
$loaderPosts = false;
if(is_embed()) {
	$tplSite = "embed";
} else if(is_404()) {
	$tplSite = "404";
} else if(is_search()) {
	$tplSite = "search";
	$loaderPosts = true;
} else if(is_front_page()) {
	$tplSite = "front_page";
} else if(is_home()) {
	$tplSite = "index";
} else if(is_tax()) {
	$tplSite = "tax";
	$loaderPosts = true;
} else if(is_single()) {
	$tplSite = "single";
} else if(is_page()) {
	$tplSite = "page";
} else if(is_singular()) {
	$tplSite = "singular";
} else if(is_category()) {
	$tplSite = "category";
	$loaderPosts = true;
} else if(is_tag()) {
	$tplSite = "tag";
	$loaderPosts = true;
} else if(is_author()) {
	$tplSite = "author";
	$loaderPosts = true;
} else if(is_date()) {
	$tplSite = "date";
	$loaderPosts = true;
} else if(is_archive()) {
	$tplSite = "archive";
	$loaderPosts = true;
}
if(is_page_template()) {
	$tplSite = get_page_template_slug();
}
if(empty($tplSite) || $tplSite=="index" || is_home()) {
	$tplSite = "index";
}
if(post_password_required()) {
?>
    <form class="form-postpass" method="post" action="/wp-login.php?action=postpass">
        <p class="form-postpass-p"><?php echo _e("This content is password protected. To view it please enter your password below:"); ?></p>
        <label for="pwbox-<?php the_ID(); ?>" class="form-postpass-label"><input type="password" size="20" id="pwbox-<?php the_ID(); ?>" name="post_password" class="form-postpass-input" style="margin:10px 0;"></label><br />
        <input type="submit" value="<?php echo _e("Submit"); ?>" class="form-postpass-submit" name="Submit"/>
    </form>
<?php
die();
}

if(!templates::exists($tplSite)) {
	trigger_error("Шаблон ".$tplSite." не найден");
	//$tplSite = "";
}

$blockNameForCycle = $tplSite;
$cardinalCache['loaderPosts'] = $loaderPosts;
$cardinalCache['blockNameForCycle'] = $blockNameForCycle;

add_action('wp_loaded', 'initial_builder');
function initial_builder() {
	global $cardinalCache;
	if(isset($cardinalCache['loaderPosts']) && isset($cardinalCache['blockNameForCycle']) && $cardinalCache['loaderPosts']===true) {
		if(have_posts()) {
			while(have_posts()) {
				the_post();
				addDataPost($post, $cardinalCache['blockNameForCycle']);
			}
			wp_reset_query();
			templates::assign_var("morePages", (more_posts() ? "1" : "0"));
		} else {

		}
	} else {
		addDataPost();
	}
	if(file_exists(PATH_SKINS."site.php")) {
		include_once(PATH_SKINS."site.php");
	}
}