<!DOCTYPE HTML>
<!--
	Landed by HTML5 UP
	html5up.net | @n33co
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)

	!!!DEPENDS ON PHP AND YOURLS!!!
-->
<html>
	<head>
		<title>(server)&sup2;</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<!--[if lte IE 8]><script src="../assets/js/ie/html5shiv.js"></script><![endif]-->
		<link rel="stylesheet" href="../assets/css/main.css" />
		<!--[if lte IE 9]><link rel="stylesheet" href="../assets/css/ie9.css" /><![endif]-->
		<!--[if lte IE 8]><link rel="stylesheet" href="../assets/css/ie8.css" /><![endif]-->
	</head>
	<body>
		<div id="page-wrapper">

			<!-- Header -->
				<?php include($_SERVER['DOCUMENT_ROOT'].'/header-alt.html'); ?>

			<!-- Main -->
				<div id="main" class="wrapper style1">
					<div class="container">
						<header class="major">
							<h2>Link Shortener [BETA]</h2>
							<p>Have a long link you want to shorten? Do it here!</p>
						</header>

						<!-- Content -->
							<section id="content">
								<h3>Create a Short URL</h3>
								<?php
									// Start YOURLS engine
									require_once( dirname(__FILE__).'/includes/load-yourls.php' );

									// Change this to match the URL of your public interface. Something like: http://your-own-domain-here.com/index.php
									$page = YOURLS_SITE . '/short/index.php' ;

									// Part to be executed if FORM has been submitted
									if ( isset( $_REQUEST['url'] ) && $_REQUEST['url'] != 'http://' ) {

										// Get parameters -- they will all be sanitized in yourls_add_new_link()
										$url     = $_REQUEST['url'];
										$keyword = isset( $_REQUEST['keyword'] ) ? $_REQUEST['keyword'] : '' ;
										$title   = isset( $_REQUEST['title'] ) ?  $_REQUEST['title'] : '' ;
										$text    = isset( $_REQUEST['text'] ) ?  $_REQUEST['text'] : '' ;

										// Create short URL, receive array $return with various information
										$return  = yourls_add_new_link( $url, $keyword, $title );

										$shorturl = isset( $return['shorturl'] ) ? $return['shorturl'] : '';
										$message  = isset( $return['message'] ) ? $return['message'] : '';
										$title    = isset( $return['title'] ) ? $return['title'] : '';
										$status   = isset( $return['status'] ) ? $return['status'] : '';

										// Stop here if bookmarklet with a JSON callback function ("instant" bookmarklets)
										if( isset( $_GET['jsonp'] ) && $_GET['jsonp'] == 'yourls' ) {
											$short = $return['shorturl'] ? $return['shorturl'] : '';
											$message = "Short URL (Ctrl+C to copy)";
											header('Content-type: application/json');
											echo yourls_apply_filter( 'bookmarklet_jsonp', "yourls_callback({'short_url':'$short','message':'$message'});" );

											die();
										}
									}

									// Part to be executed if FORM has been submitted
									if ( isset( $_REQUEST['url'] ) && $_REQUEST['url'] != 'http://' ) {

										// Display result message of short link creation
										if( isset( $message ) ) {
											echo "<h2><center>$message</h2></center>";
										}

										if( $status == 'success' ) {
											// Include the Copy box and the Quick Share box
											// yourls_share_box( $url, $shorturl, $title, $text );

											// Initialize clipboard -- requires js/share.js and js/jquery.zclip.min.js to be properly loaded in the <head>
											// echo "<script>init_clipboard();</script>\n";
										}

									// Part to be executed when no form has been submitted
									} else {

											$site = YOURLS_SITE;

											// Display the form
											echo <<<HTML
											<form method="post" action="">
												<div class="row uniform 50%">
													<div class="12u$">
														<input type="text" name="url" value="" placeholder="Long URL - http://example.com" />
													</div>
													<div class="6u 12u$(xsmall)">
														<input type="text" name="keyword" value="" placeholder="Custom short URL (Optional)" />
													</div>
													<div class="6u$ 12u$(xsmall)">
														<input type="text" name="title" value="" placeholder="Custom name (Optional)" />
													</div>
													<div class="12u$">
														<ul class="actions">
															<li><input type="submit" value="Shorten URL" class="special" /></li>
															<li><input type="reset" value="Reset" /></li>
														</ul>
													</div>
											</form>
HTML;

									}
								?>
							</section>

							<section id="bookmarklets">
								<h3>Bookmarklets [ALPHA]</h2>
								<p>
									<a href="javascript:(function()%7Bvar%20d=document,w=window,enc=encodeURIComponent,e=w.getSelection,k=d.getSelection,x=d.selection,s=(e?e():(k)?k():(x?x.createRange().text:0)),s2=((s.toString()=='')?s:enc(s)),f='<?php echo $page; ?>',l=d.location,p='?url='+enc(l.href)+'&title='+enc(d.title)+'&text='+s2,u=f+p;try%7Bthrow('ozhismygod');%7Dcatch(z)%7Ba=function()%7Bif(!w.open(u))l.href=u;%7D;if(/Firefox/.test(navigator.userAgent))setTimeout(a,0);else%20a();%7Dvoid(0);%7D)()" class="bookmarklet">Default</a><br />
									<a href="javascript:(function()%7Bvar%20d=document,w=window,enc=encodeURIComponent,e=w.getSelection,k=d.getSelection,x=d.selection,s=(e?e():(k)?k():(x?x.createRange().text:0)),s2=((s.toString()=='')?s:enc(s)),f='<?php echo $page; ?>',l=d.location,k=prompt(%22Custom%20URL%22),k2=(k?'&keyword='+k:%22%22),p='?url='+enc(l.href)+'&title='+enc(d.title)+'&text='+s2+k2,u=f+p;if(k!=null)%7Btry%7Bthrow('ozhismygod');%7Dcatch(z)%7Ba=function()%7Bif(!w.open(u))l.href=u;%7D;if(/Firefox/.test(navigator.userAgent))setTimeout(a,0);else%20a();%7Dvoid(0)%7D%7D)()" class="bookmarklet">Custom</a><br />
									<a href="javascript:(function()%7Bvar%20d=document,s=d.createElement('script');window.yourls_callback=function(r)%7Bif(r.short_url)%7Bprompt(r.message,r.short_url);%7Delse%7Balert('An%20error%20occured:%20'+r.message);%7D%7D;s.src='<?php echo $page; ?>?url='+encodeURIComponent(d.location.href)+'&jsonp=yourls';void(d.body.appendChild(s));%7D)();" class="bookmarklet">Popup</a><br />
									<a href="javascript:(function()%7Bvar%20d=document,k=prompt('Custom%20URL'),s=d.createElement('script');if(k!=null){window.yourls_callback=function(r)%7Bif(r.short_url)%7Bprompt(r.message,r.short_url);%7Delse%7Balert('An%20error%20occured:%20'+r.message);%7D%7D;s.src='<?php echo $page; ?>?url='+encodeURIComponent(d.location.href)+'&keyword='+k+'&jsonp=yourls';void(d.body.appendChild(s));%7D%7D)();" class="bookmarklet">Custom Popup</a><br />
								</p>
							</section>

					</div>
				</div>

			<!-- Footer -->
				<?php include($_SERVER['DOCUMENT_ROOT'].'/footer-alt.html'); ?>

		</div>

		<!-- Scripts -->
			<script src="../assets/js/jquery.min.js"></script>
			<script src="../assets/js/jquery.scrolly.min.js"></script>
			<script src="../assets/js/jquery.dropotron.min.js"></script>
			<script src="../assets/js/jquery.scrollex.min.js"></script>
			<script src="../assets/js/skel.min.js"></script>
			<script src="../assets/js/util.js"></script>
			<!--[if lte IE 8]><script src="../assets/js/ie/respond.min.js"></script><![endif]-->
			<script src="../assets/js/main.js"></script>

	</body>
</html>