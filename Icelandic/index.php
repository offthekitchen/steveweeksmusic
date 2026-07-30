<?php
	//Don't Display Notice messages from PHP Server
	error_reporting(E_ALL ^ E_NOTICE);

	//This include defines the relative path to the root directory from this sub-directory
 	include_once ("root.inc.php");
	
	//inlcude web site settings
	include_once ($ROOT . "/includes/websiteSettings.php");

	//inlcude web site settings
	include_once (SETTINGS_DIR . "/SteveWeeksMusicSettings.php");

	//Common Functions
   	include (INCLUDE_DIR . "/commonFunctions.php");

	//Page Name 
	$sPageName = "Steve's Icelandic Resources";
	$sPageTitle = "Steve's Icelandic Resources";
	//Custom FB Image
	$sFBImage = "FB_Icelandic.png";


	$sActiveMenuItem = HOME_ACTIVE;	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<?php
	//Inlcude a common <HEAD> section
   include (INCLUDE_DIR . "/HTMLHead.php");
?>
<style>

.icelandic-music-item {
	min-height: 110px;
}
</style>
<BODY>
	<div class="container-fluid">
		<div class="row">
		<?php
			include(INCLUDE_DIR . "/header.php");
		?>		
		</div>
		<div class="row">
			<aside class="col-xs-12 col-sm-10 col-sm-push-1">
				<div class="orange-frame frame-box in-front-of-theme-image">
					<div class="thanks">
					<p>Here are a few of my favorite Icelandic language resources.  These are cetainly not all of the resources out there, nor are they they necessarily the best for everyone.  These are just the ones that I've actually used and which seem to work well for me.</p>   
					</div>
				</div>
			</aside>
			<main id="main-content" class="col-xs-12">
				<section class="row showcase-section">
					<div class="col-xs-12 col-sm-6 col-lg-2">
						<a href="#community">
							<div class="showcase-box presskit-showcase behind-theme-image yellow-box">
								Community
							</div>
						</a>
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-2">
						<a href="#courses">
							<div class="showcase-box presskit-showcase behind-theme-image purple-box">
								Courses
							</div>
						</a>
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-2">
						<a href="#books">
							<div class="showcase-box presskit-showcase in-front-of-theme-image green-box">
								Books
							</div>
						</a>
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-2">
						<a href="#translation">
							<div class="showcase-box presskit-showcase behind-theme-image red-box">
								Translation
							</div>
						</a>
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-2">
						<a href="#music">
							<div class="showcase-box presskit-showcase behind-theme-image blue-box">
								Music
							</div>
						</a>
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-2">
						<a href="#movies">
							<div class="showcase-box presskit-showcase behind-theme-image orange-box">
								Movies/TV
							</div>
						</a>
					</div>

				</section>
				<div class="row">
					<div class="col-xs-12 full-width-xs">
						<header class="presskit-section-header yellow-box">
						<a name="community">
							Community
						</a>
						</header>
					</div>
				</div>
				<div class="row">	
					<div class="col-xs-12">
						<div class="frame-box yellow-frame" style="padding: 15px;">
							<div class="thanks">
								<p>Unless you are lucky enough to actually live in Iceland, studying the language can be a solitary endeavor.  However, there are some ways to connect with others learning Icelandic as well as native speakers.  Here are a couple that I've found.</p>
							</div>
						</div>								
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item">
						<b><a href="https://www.facebook.com/groups/PracticeLearnIcelandic/" target="_blank">Practice and Learn Icelandic (Facebook) </a></b><br>
						<i>By far one of the most fun resources that I use.  This group is well-moderated, very active and welcoming.  The subject matter centers around the Icelandic language although you'll find posts about Icelandic culture as well.  I highly recommend joining and being an active participant in this group.</i> 
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item">
						<b><a href="https://www.reddit.com/r/learnIcelandic/" target="_blank">Learn Icelandic (Reddit) </a></b><br>
						<i>I'm not a huge Reddit user, but there are lots of questions and discussions here.  I occassionally check this out to see what's going on. </i> 
					</div>					
				</div>	
				<div class="row">
					<div class="col-xs-12 full-width-xs">
						<header class="presskit-section-header purple-box">
						<a name="courses">
							Courses
						</a>
						</header>
					</div>
				</div>
				<div class="row">	
					<div class="col-xs-12">
						<div class="frame-box purple-frame" style="padding: 15px;">
							<div class="thanks">
								When you decide to learn Icelandic, it may seem at first that there aren't any resources.  That's only because the resources aren't in the usual places (Rosetta Stone e.g.).  But once you poke around, you find the opposite problem.  There's almost too many books, web sites, courses and tools to choose from.  These are the ones that I've used. 
							</div>
						</div>								
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item">
						<b><a href="https://www.amazon.com/Beginners-Icelandic-Hippocrene-Helga-Hilmisdottir/dp/0781811910" target="_blank">Beginners Icelandic</a></b><br>
						<i>This is my Icelandic course book of choice.  There are others, but I found that this one is organized and flows in a way that works well for me.  2 Audio CDs accompany the material.  I study the written material and then listen to the CDs while I'm driving to reinforce my understanding.</i> 
					</div>			
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item">
						<b><a href="https://languagedrops.com/" target="_blank">Drops mobile app </a></b><br>
						<i>This is the best app I've found for increasing your vocabulary and hearing pronunciation.  It won't really help with declension or conjugations, 
						but you'll soon find youself walking around the house pointing at things and saying "strauj&aacute;rn", "&ouml;rbylgjuofn", and "innstunga"!</i> 
					</div>		
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item">
						<b><a href="http://tungumalatorg.is/viltu_laera_islensku/en" target="_blank">Viltu L&aelig;ra &Iacute;slensku  (TV Episodes)</a></b><br>
						<i>21 episodes intended to teach Icelandic. Each episode is 20 minutes long, and accompanied by activities to re-inforce the subject matter.  The drawbacks are that there doesn't seem to be a way to change the video to full screen, and from the very first episode, the dialog is fast and complex.  The good news is that they are totally free and the exercises are really well done.  I watch an episode as I'm working out and then complete the exercises using Google Translate to help me understand.  I find that it helps me to then re-watch the episode after I've studied the subject matter a little.</i> 
					</div>			
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item">
						<b><a href="https://icelandiconline.com/" target="_blank">Icelandic Online</a></b><br>
						<i>Honestly, I haven't gotten around to really using this course a ton, but I have tried it out, and it seems very thorough and well constructed.  It also gets recommended quite a bit by other folks, so I'm including it here.  I'll report back more as I use it in the future.</i> 
					</div>					
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item">
						<b><a href="https://islenskafyriralla.wordpress.com/islenskards/" target="_blank">Printable Flash Cards </a></b><br>
						<i>These cards are amazing!  They are color coded by verb category and have quick icons to let you know about things like case and vowel shift.  All conjugations are listed as well as translations for English and Spanish ...a lot of information on each card!  The drawback?  You'll have to print them yourself, which can be very expensive and time-consuming.</i> 
					</div>	
				
				</div>	
				<div class="row">
					<div class="col-xs-12 full-width-xs">
						<header class="presskit-section-header green-box">
						<a name="books">
							Books
						</a>
						</header>
					</div>
				</div>
				<div class="row">	
					<div class="col-xs-12">
						<div class="frame-box green-frame" style="padding: 15px;">
							<div class="thanks">
								Reading Icelandic books may be an unlikely way for a beginner to study the language, but there are a few books that have helped me.  After all, it's how we teach a language in school right?  Well, at least in the days before laptops and tablets.   
							</div>
						</div>								
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item">
						<b><a href="https://www.amazon.com/Short-Stories-Icelandic-Beginners-Richards/dp/1529302994" target="_blank">Short Stories in Icelandic for Beginners</a> (by Olly Richards)</b><br>
						<i>This has been one of the best reading resources for me personally.&nbsp;&nbsp;I have to say, that I had been studying Icelandic for a little while
						    before I bought this book, so I don't know if it's all that helpful on it's own for a complete beginner.&nbsp;&nbsp;However, given my novice level,
							it is perfect.  the stories are short and repetitive and each story tends to build on the vocabulary of the previous ones.&nbsp;&nbsp;There is a 
							small review, vocabulary list and test questions at the end of each chapter.&nbsp;&nbsp;The stories are interesting enough to keep you reading
							without being overly complex.&nbsp;&nbsp;I was amazed to find that I could read and understand these right off the bat.&nbsp;&nbsp;What's more, there
							are versions of this book for other languages.&nbsp;&nbsp;I got the Spanish version and saw that the stories appear to be the same.&nbsp;&nbsp;
							This might help if you're learning multiple languages.&nbsp;&nbsp;Highly recommend!</i> 
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item">
						<b><a href="https://www.utgafuhus.is/en/products/arstidir-karitas-hrundar-palsdottir" target="_blank">&Aacute;rst&iacute;&eth;ir</a> (by Kar&iacute;tas Hrundar P&aacute;lsd&oacute;ttir)</b><br>
						<i>If you're about where I am in your studies (expanding vocabulary but struggling with grammar), this book is a real gem.&nbsp;&nbsp;
						The stories are simple and short, very short.&nbsp;&nbsp;Some are only a page long.&nbsp;&nbsp;But here's the thing; they're really nicely
						written and endearing.&nbsp;&nbsp;Usually books intended to teach an language sacrifice plot and style for the sake of repetition and practice.
						&nbsp;&nbsp;But these stories are elegant and nice to read.&nbsp;&nbsp;I can tell that Kar&iacute;tas Hrundar P&aacute;lsd&oacute;ttir, the author, has really
						put some thought into the order that words are introduced and creating an arc of learning while still keeping her eye on the works as literature.
						&nbsp;&nbsp;They couldn't be better for getting some Icelandic pratice in during the quiet times in between busy life.&nbsp;&nbsp;And what's more, the paperback
						edition just feels great to hold.&nbsp;&nbsp;It's a high quality book that you'll love having on your nightside table.&nbsp;&nbsp;And no, I in no way
						receive any comissions from this book.&nbsp;&nbsp;I just like it.</i> 
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item">
						<b><a href="https://www.amazon.com/gp/product/1481241915/" target="_blank">Eir&iacute;ks Saga Rau&eth;a</a> (editted by VolundR Lars Agnarsson)</b><br>
						<i>I'm a very goal-oriented person, so when I decided to learn Icelandic, I wanted to set some goals to work toward. One of these was to read and undertand at least one Viking saga.  I think it's fascinating that Icelanders can still comprehend the sagas.  This particular edition is great because it has the saga in both Icelandic and English.  It also has the saga in original Old Norse in case you want to delve a little deeper.</i> 
					</div>

					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item">
						<b><a href="http://vefir.nams.is/smabokaskapur/" target="_blank">Children's Books in Icelandic</a></b><br>
						<i>This site has Icelandic children's books.  The plots may be a bit simple, but so is the text.</i> 
					</div>										
				</div>	
				<div class="row">
					<div class="col-xs-12 full-width-xs">
						<header class="presskit-section-header red-box">
						<a name="translation">
							Translation
						</a>
						</header>
					</div>
				</div>
				<div class="row">	
					<div class="col-xs-12">
						<div class="frame-box red-frame" style="padding: 15px;">
							<div class="thanks">
								No matter what method you use to study Icelandic, you're gonng find times when you need to conjugate or translate something.  There are tons of translation tools out there.  These seem to work well.
							</div>
						</div>								
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item">
						<b><a href="https://www.google.com/?gws_rd=ssl#q=icelandic+translator" target="_blank">
						Google translator</a></b><br>
						<i>It's not perfect, but it's easy to use and it's improving everyday.</i> 
					</div>	
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item">
						<b><a href="https://www.amazon.com/Icelandic-English-English-Icelandic-Practical-Dictionary-Helmisdottir/dp/0781813514/ref=pd_sbs_14_1?_encoding=UTF8&pd_rd_i=0781813514&pd_rd_r=6238778a-f014-11e8-bbc4-8bb98d0238f1&pd_rd_w=HSRe9&pd_rd_wg=xbsPC&pf_rd_i=desktop-dp-sims&pf_rd_m=ATVPDKIKX0DER&pf_rd_p=7d5d9c3c-5e01-44ac-97fd-261afd40b865&pf_rd_r=3FFDZS6RP7B4RVBD4JHV&pf_rd_s=desktop-dp-sims&pf_rd_t=40701&psc=1&refRID=3FFDZS6RP7B4RVBD4JHV" target="_blank">
						Icelandic/English Dictionary</a></b><br>
						<i>This is the best Icelandic/English dictionary that I've found.  It's produced by Helga Helmisd&oacute;ttir who also created the Beginner's Icelandic Course book that I've used.</i> 
					</div>					
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item">
						<b><a href="http://cooljugator.com/is" target="_blank">
						Verb Conjugator</a></b><br>
						<i>This site conjugates about 1000 different verbs.  Type in the verb in English or Icelandic and get back past, present and future conjugations.</i> 
					</div>					
				</div>	
				<div class="row">
					<div class="col-xs-12 full-width-xs">
						<header class="presskit-section-header blue-box">
						<a name="music">
							Music
						</a>
						</header>
					</div>
				</div>
				<div class="row">	
					<div class="col-xs-12">
						<div class="frame-box blue-frame" style="padding: 15px;">
							<div class="thanks">
								OK, so you can't learn Icelandic just by listening to music, but this is supposed to be fun right?  It can be really encouraging when you start to recognize words and phrases in songs.  Also let's face it, Iceland has an amazing music scene.  I've listed some groups and songs below that are in Icelandic and that are sung slow enough for a beginner to understand or that have videos with lyrics captions.  There are MANY more fantastic groups not listed, and you should check them out if you just want to hear some great music from Iceland.    
							</div>
						</div>								
					</div>
				</div>	
				<div class="row">	
				<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item icelandic-music-item">
						<b>Thurdy (weird)</b><br>
						<i>
							<ul>
								<li>
									<b><a href="https://youtu.be/5xnCXpvhQHg" target="_blank">Venjulegur Dagur (Icelandic Version) </a></b>									
								</li>
								<li>
									<b><a href="https://youtu.be/tkVbshyANV4" target="_blank">Venjulegur Dagur (English Version) </a></b>									
								</li><li>
									<b><a href="https://youtu.be/JLC8mOQPx5U" target="_blank">&Eacute;g er a&eth; L&aelig;ra </a></b>									
								</li>
							</ul>
						</i> 
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item icelandic-music-item">
						<b>Sigur R&oacute;s (rock)</b><br>
						<i>
							<ul>
								<li>
									<b><a href="https://www.youtube.com/watch?v=K19cg1yomHY" target="_blank">Hoppipolla (with lyrics) </a></b>									
								</li>
								<li>
									<b><a href="https://www.youtube.com/watch?v=_Q3Yttx0PCA" target="_blank">Inn&iacute; Mer Syngur Vitleysingur (with lyrics)</a></b>									
								</li>
							</ul>
						</i> 
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item icelandic-music-item">
						<b>Lj&oacute;tu Halfvitarnir (Punk Folk?  Celtic Rock? Drinking Songs? ...Total Fun )</b><br>
						<i>
							<ul>
								<li>
									<b><a href="https://www.youtube.com/watch?v=mvCrxphe9LU" target="_blank">Vi&eth; St&ouml;ndum H&eacute;r Enn (with Lyrics)</a></b>									
								</li>
								<li>
									<b><a href="https://www.youtube.com/watch?v=ffjVjX23X80" target="_blank">Hosil&oacute; (with lyrics)</a></b>									
								</li>
							</ul>
						</i> 
					</div>		
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item icelandic-music-item">
						<b>&Aacute;rst&iacute;&eth;ir (Chamber/Pop)</b><br>
						<i>
							<ul>
								<li>
									<b><a href="https://www.youtube.com/watch?v=1uSMqx7OCqI" target="_blank">Lj&oacute;&eth; &iacute; sand (with lyrics) </a></b>									
								</li>
								<li>
									<b><a href="https://www.youtube.com/watch?v=_xnuPm-PSjU" target="_blank">&Aacute; me&eth;an j&ouml;r&eth;in sefur</a></b>									
								</li>
							</ul>
						</i> 
					</div>		
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item icelandic-music-item">
						<b>&Aacute;sgeir Trausti (Melodic Folk Pop)</b><br>
						<i>
							<ul>
								<li>
									<b><a href="https://www.youtube.com/watch?v=ZGWS7NubmMc" target="_blank">H&aelig;rra</a></b>									
								</li>
								<li>
									<b><a href="https://www.youtube.com/watch?v=kszE1Mn4Alg" target="_blank">N&yacute;falli&eth; regn (with lyrics)</a></b>									
								</li>
								<li>
									<b><a href="https://www.youtube.com/watch?v=a9QC5ZSqhYw" target="_blank">Leyndarm&aacute;l (with lyrics)</a></b>									
								</li>
							</ul>
						</i> 
					</div>			
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item icelandic-music-item">
						<b>Emmsj&eacute; Gauti (rap/ hip hop)</b><br>
						<i>
							<ul>
								<li>
									<b><a href="https://www.youtube.com/watch?v=MKDVf2eojJ8" target="_blank">Okkar Lei&eth; (with lyrics)</a></b>									
								</li>
								<li>
									<b><a href="https://www.youtube.com/watch?v=OZD2AZhyriM" target="_blank">&Aacute; me&eth;an &eacute;g er ungur (with lyrics)</a></b>									
								</li>
							</ul>
						</i> 
					</div>	
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item icelandic-music-item">
						<b>Sk&aacute;lm&ouml;ld (Metal)</b><br>
						<i>
							<ul>
								<li>
									<b><a href="https://www.youtube.com/watch?v=67dV-va7uto" target="_blank">&Aacute;r&aacute;s (with lyrics)</a></b>									
								</li>
								<li>
									<b><a href="https://www.youtube.com/watch?v=fzRy-H_HIF8" target="_blank">Ni&eth;avellir (with lyrics)</a></b>									
								</li>
							</ul>
						</i> 
					</div>														
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item icelandic-music-item">
						<b>V&ouml;k (alternative dance)</b><br>
						<i>
							<ul>
								<li>
									<b><a href="https://www.youtube.com/watch?v=8Kvc_WadMIc" target="_blank">&Eacute;g b&iacute;&eth; &thorn;&iacute;n</a></b>									
								</li>
								<li>
									<b><a href="https://www.youtube.com/watch?v=Hy7Ws7evRa0" target="_blank">Vi&eth; V&ouml;kum</a></b>									
								</li>
							</ul>
						</i> 
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item icelandic-music-item">
						<b>S&oacute;lstafir (Metal)</b><br>
						<i>
							<ul>
								<li>
									<b><a href="https://www.youtube.com/watch?v=XmGdSOhBx8E" target="_blank">Fjara </a></b>									
								</li>
								<li>
									<b><a href="https://www.youtube.com/watch?v=R8n8Uy5KmvU" target="_blank">L&aacute;gn&aelig;tti </a></b>									
								</li>
							</ul>
						</i> 
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item icelandic-music-item">
						<b>Hera Hjartard&oacute;ttir (Acoustic Folk)</b><br>
						<i>
							<ul>
								<li>
									<b><a href="https://www.youtube.com/watch?v=FnKqT_tlxfs" target="_blank">Hafi&eth; &thorn;ennan dag</a></b>									
								</li>
								<li>
									<b><a href="https://www.youtube.com/watch?v=bN0uil5e1hI" target="_blank">St&uacute;lkan sem starir &aacute; hafi&eth;</a></b>									
								</li>
							</ul>
						</i> 
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item icelandic-music-item">
						<b>Bubbi (Folk Rock) </b><br>
						<i>
							<ul>
								<li>
									<b><a href="https://www.youtube.com/watch?v=bChY8kTokG8" target="_blank">Aldrei f&oacute;r &eacute;g su&eth;ur </a></b>									
								</li>
								<li>
									<b><a href="https://www.youtube.com/watch?v=7jzmrmCdElM" target="_blank">Sem Aldrei Fyrr</a></b>									
								</li>
							</ul>
						</i> 
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item icelandic-music-item">
						<b>Hjaltal&iacute;n (Indie)</b><br>
						<i>
							<ul>
								<li>
									<b><a href="https://www.youtube.com/watch?v=bbWlO1sXSqA" target="_blank">Engill Alheimsins</a></b>									
								</li>
								<li>
									<b><a href="https://www.youtube.com/watch?v=QULPMukInds" target="_blank">&THORN;&uacute; Komst Vi&eth; Hjarta&eth; &iacute; M&eacute;r</a></b>									
								</li>
							</ul>
						</i> 
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item icelandic-music-item">
						<b>Hj&aacute;lmar (Reggae)</b><br>
						<i>
							<ul>
								<li>
									<b><a href="https://www.youtube.com/watch?v=sGoP1lEHPZ4" target="_blank">Borgin</a></b>									
								</li>
								<li>
									<b><a href="https://www.youtube.com/watch?v=tQvVoURwZy8" target="_blank">&Eacute;g vil f&aacute; m&eacute;r k&aelig;rustu</a></b>									
								</li>
							</ul>
						</i> 
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item icelandic-music-item">
						<b>Svavar Knutur (Singer/Songwriter)</b><br>
						<i>
							<ul>
								<li>
									<b><a href="https://www.youtube.com/watch?v=gcM4CTYC408" target="_blank">N&aelig;turlj&oacute;&eth; &uacuter Fj&ouml;r&eth;um </a></b>									
								</li>
								<li>
									<b><a href="https://www.youtube.com/watch?v=aP2CTbfUxdg" target="_blank">Yfir Hola Og Yfir H&aelig;dir</a></b>									
								</li>
							</ul>
						</i> 
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item icelandic-music-item">
						<b>&Oacute;l&ouml;f Arnalds (Folk)</b><br>
						<i>
							<ul>
								<li>
									<b><a href="https://www.youtube.com/watch?v=mDlN99b0EBs" target="_blank">&Eacute;g Umvef Hjarta Mitt</a></b>									
								</li>
								<li>
									<b><a href="https://www.youtube.com/watch?v=AQwiHg124S4" target="_blank">Vittu Af M&eacute;r</a></b>									
								</li>
							</ul>
						</i> 
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item icelandic-music-item">
						<b>MAMM&Uacute;T (Indie Rock)</b><br>
						<i>
							<ul>
								<li>
									<b><a href="https://www.youtube.com/watch?v=oZUgfEbxPzs" target="_blank">GL&AElig;&ETH;UR</a></b>									
								</li>
								<li>
									<b><a href="https://www.youtube.com/watch?v=5blBP22IDlc" target="_blank">Rau&eth;il&aelig;kur</a></b>									
								</li>
							</ul>
						</i> 
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item icelandic-music-item">
						<b>R&ouml;kkurr&oacute; (Alternative)</b><br>
						<i>
							<ul>
								<li>
									<b><a href="https://www.youtube.com/watch?v=rrqu8bTO6BQ" target="_blank">S&oacute;lin mun sk&iacute;na</a></b>									
								</li>
								<li>
									<b><a href="https://www.youtube.com/watch?v=ip8vCnReoNE" target="_blank">Svanur</a></b>									
								</li>
							</ul>
						</i> 
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item icelandic-music-item">
						<b>Kaleo (Rock)</b><br>
						<i>
							<ul>
								<li>
									<b><a href="https://www.youtube.com/watch?v=Da5qQD_RpEQ" target="_blank">Vor &iacute; Vaglask&oacute;gi</a></b>									
								</li>
							</ul>
						</i> 
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item icelandic-music-item">
						<b>Salka S&oacute;l (pop)</b><br>
						<i>
							<ul>
								<li>
									<b><a href="https://www.youtube.com/watch?v=Dq2kaCX0xPw" target="_blank">&Aacute; annan sta&eth; </a></b>									
								</li>
							</ul>
						</i> 
					</div>					
				</div>	
				<div class="row">
					<div class="col-xs-12 full-width-xs">
						<header class="presskit-section-header orange-box" >
						<a name="movies" style="color: white;">
							Movies
						</a>
						</header>
					</div>
				</div>
				<div class="row">	
					<div class="col-xs-12">
						<div class="frame-box orange-frame" style="padding: 15px;">
							<div class="thanks">
								I love movies ...and Iceland produces a lot of really great flicks.  Like most Scandanavian films, Icelandic movies tend toward the darker side though not always.  The dialog in movies is often softly spoken, complex, and fast, so a beginner like me cannot understand everything that's being said.  But that's what captions are for!  I tend to watch these moves over and over as I exercise, and I find that with each viewing I understand a little more because of my other studies.
							</div>
						</div>								
					</div>
				</div>	
				<div class="row">	
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item">
						<b><a href="https://www.icelandiccinema.com/" target="_blank">Icelandic Cinema Online</a></b><br>
						<i>You can buy and watch Icelandic movies from this site.  There are a few free ones too.  Just click on the "free" tab.</i> 
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item">
						<b><a href="https://www.imdb.com/title/tt0805576/" target="_blank">M&yacute;rin (Jar City)</a></b><br>
						<i>Based on the popular Icelandic "Erlendur" crime series, this is one of my favorite movies.  It's a great crime mystery and well acted and produced.  It also has one of my favorite lines in an Icelandic movie "Typical Icelandic murder, messy and pointless"</i> 
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item">
						<b><a href="https://www.imdb.com/title/tt2374902/" target="_blank">Metalhead</a></b><br><i>Great movie about a young girl who uses metal music to deal with the death of her brother. </i> 
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item">
						<b><a href="https://www.imdb.com/title/tt0237993/" target="_blank">101 Reykjav&iacute;k</a></b><br><i>Classic Icelandic comedy about a guy we would call a "slacker" here in the US.</i> 
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item">
						<b><a href="https://www.imdb.com/title/tt0351461/" target="_blank">N&oacute;i the Albino</a></b><br><i>Tragic and funny and quirky and beautifully filmed.  There are scenes that seem put together more like a painting than a movie.</i> 
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item">
						<b><a href="https://www.imdb.com/title/tt1114712/" target="_blank">Br&uacute;&eth;guminn (White Night Wedding)</a></b><br><i>Dark comedy about a wedding on a little island.  Margr&eacute;t Vilhj&aacute;lmsd&oacute;ttir is brilliant in this one in her potrayal of the troubled Anna ...and we always love &Oacute;lafur Darri &Oacute;lafsson (you may recognize him as the helicopter pilot in "The Secret Life of Walter Mitty" and appearing in many of the other films in this list).</i> 
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item">
						<b><a href="https://www.imdb.com/title/tt0840046/" target="_blank">Astr&oacute;p&iacute;a (Dorks & Damsels)</a></b><br>
						<i>OK, this one is a little low-budget and campy, but it is endearing and fun.  The premise is a beautiful girl who has to take a job in a store for nerds because her rich boyfriend goes to jail.  If you've ever played Dungeons and Dragons or watched Star Wars more than once, you'll like this one. I can't believe that Hollywood hasn't stolen this plot!</i> 
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item">
						<b><a href="https://www.imdb.com/title/tt1764275/" target="_blank">Dj&uacute;pi&eth; (The Deep)</a></b><br>
						<i>A true story about a guy (&Oacute;lafur Darri &Oacute;lafsson) who survives a night in the freezing waters off of Iceland after his fishing boat sinks.  Not a ton of dialog, but a very engaging film.</i> 
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item">
						<b><a href="https://www.imdb.com/title/tt0281176/" target="_blank">M&aacute;vahl&aacute;tur (The Seagull's Laughter)</a></b><br>
						<i>Very funny film about a small Icelandic town post World War II.  Margr&eacute;t Vilhj&aacute;lmsd&oacute;ttir is a femme fatale returning from life in the US who turns the town upsidedown.</i> 
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item">
						<b><a href="https://www.imdb.com/title/tt2009643/" target="_blank">&Aacute; annan veg (Either Way)</a></b><br>
						<i>This is a very smart comedy about 2 guys who are painting the lines on the roads in the interior of Iceland. This was remade in the United States as "Prince Avalanche" starring Paul Rudd and Emile Hirsch.</i> 
					</div>					
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item">
						<b><a href="https://www.imdb.com/title/tt3561180/" target="_blank">&Oacute;f&aelig;r&eth;(Trapped)</a></b><br>
						<i>OK, this one is a TV show, but it's really really good.  Created by Baltasar Korm&aacute;kur, it's a murder mystery that takes place in a remote Icelandic town 
						and stars &Oacute;lafur Darri &Oacute;lafsson (The Deep, White Night Wedding), Ilmur Kristj&aacute;nsd&oacute;ttir (White Night Wedding) and Ingvar Eggert Sigur&eth;sson (Jary City, Metalhead)...wow. </i> 
					</div>
					<div class="col-xs-12 col-sm-6 col-lg-4 icelandic-item">
						<b><a href="https://www.imdb.com/title/tt11102190/" target="_blank">Katla</a></b><br>
						<i>Another TV show set in V&iacute;k with Ingvar Eggert Sigur&eth;sson and other familiar faces.  It's a supernatural story about people who have been
							missing for years suddenly walking out of the glacier.  Like a polar opposite to &Oacute;f&aelig;r&eth; where the show is constantly during a 
							white-out blizzard, this little town is always covered in black ash from the volcano.  Creepy.</i> 
					</div>
				</div>	
			</main>
		</div>
		<div class="row">
		<?php
			include(INCLUDE_DIR . "/footer.php");
		?>		
		</div>
	</div>
	<style>
		.icelandic-item { padding-bottom: 15px; }
	</style>
</BODY>
</HTML>
