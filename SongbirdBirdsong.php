<?php
//Don't Display Notice messages from PHP Server
error_reporting(E_ALL ^ E_NOTICE);

//This include defines the relative path to the root directory from this sub-directory
include_once("root.inc.php");

//inlcude web site settings
include_once($ROOT . "/includes/websiteSettings.php");

//inlcude web site settings
include_once(SETTINGS_DIR . "/SteveWeeksMusicSettings.php");

//Common Functions
include(INCLUDE_DIR . "/commonFunctions.php");

//include Review Class
include_once(CLASS_DIR . "/class_Review.php");

//include Award Class
include_once(CLASS_DIR . "/class_Award.php");

//Page Name 
$sPageName = "Songbird Birdsong";
$sPageTitle = "Songbird / Birdsong 45 RPM Single";
//Custom FB Image
$sFBImage = "FB_Songbird.png";


$sActiveMenuItem = ABOUT_ACTIVE;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<?php
//Inlcude a common <HEAD> section
include(INCLUDE_DIR . "/HTMLHead.php");
?>

<STYLE>
    @font-face {
        font-family: GraublauWeb;
        src: url("path/GraublauWeb.otf") format("opentype");
    }

    /* Style the Image Used to Trigger the Modal */
    #myImg {
        border-radius: 5px;
        cursor: pointer;
        transition: 0.3s;
    }

    #myImg:hover {
        opacity: 0.7;
    }

    /* The Modal (background) */
    .modal {
        display: none;
        /* Hidden by default */
        position: fixed;
        /* Stay in place */
        z-index: 300;
        /* Sit on top */
        padding-top: 100px;
        /* Location of the box */
        left: 0;
        top: 0;
        width: 100%;
        /* Full width */
        height: 100%;
        /* Full height */
        overflow: auto;
        /* Enable scroll if needed */
        background-color: rgb(0, 0, 0);
        /* Fallback color */
        background-color: rgba(0, 0, 0, 0.9);
        /* Black w/ opacity */
    }

    /* Modal Content (Image) */
    .modal-content {
        margin: auto;
        display: block;
        width: 400px;
        max-width: 700px;
    }

    /* Caption of Modal Image (Image Text) - Same Width as the Image */
    #caption {
        margin: auto;
        display: block;
        width: 80%;
        max-width: 700px;
        text-align: center;
        color: #ccc;
        padding: 10px 0;
        height: 150px;
    }

    /* Add Animation - Zoom in the Modal */
    .modal-content,
    #caption {
        animation-name: zoom;
        animation-duration: 0.6s;
    }

    @keyframes zoom {
        from {
            transform: scale(0)
        }

        to {
            transform: scale(1)
        }
    }

    /* The Close Button */
    .close {
        position: absolute;
        top: 15px;
        right: 35px;
        color: #f1f1f1;
        font-size: 40px;
        font-weight: bold;
        transition: 0.3s;
    }

    .close:hover,
    .close:focus {
        color: #bbb;
        text-decoration: none;
        cursor: pointer;
    }

    /* 100% Image Width on Smaller Screens */
    @media only screen and (max-width: 700px) {
        .modal-content {
            width: 100%;
        }
    }
</STYLE>

<script>


    function showImage(birdNumber, birdName) {

        var modal = document.getElementById("imageModal");
        var modalImg = document.getElementById("img01");
        var captionText = document.getElementById("caption");
        modal.style.display = "block";
        modalImg.src = `<?php echo IMG_DIR; ?>/birds/${birdNumber}.png`
        captionText.innerHTML = birdName;
        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks on <span> (x), close the modal
        modal.onclick = function() {
            modal.style.display = "none";
        }

    }
</script>

<BODY>

    <!-- The Modal -->
    <div id="imageModal" class="modal">

        <!-- The Close Button -->
        <span class="close">&times;</span>

        <!-- Modal Content (The Image) -->
        <img class="modal-content" id="img01">

        <!-- Modal Caption (Image Text) -->
        <div id="caption"></div>
    </div>

    <?php
    $birds = [
        "Chicken" => array("SOLD", true),
        "Swan" => array("SOLD", true),
        "Heron" => array("SOLD",true),
        "Hawk" => array("REQUEST", false),
        "Kookaburra" => array("SOLD", true),
        "Currawong" => array("AVAILABLE", true),
        "Wren" => array("SOLD", true),
        "Robin" => array("SOLD", true),
        "Cockatoo" => array("REQUEST", false),
        "Turtle Dove" => array("REQUEST", false),
        "Grey Goose" => array("REQUEST", false),
        "Frogmouth" => array("REQUEST", false),
        "Barn Owl" => array("SOLD", true),
        "Yellow-knobbed Curassow" => array("REQUEST", false),
        "Peacock" => array("SOLD", true),
        "Xenops" => array("AVAILABLE", true),
        "Black-Footed Albatross" => array("REQUEST", false),
        "Grackle" => array("SOLD", true),
        "Gamecock" => array("SOLD", true),
        "Finch" => array("SOLD", true),
        "Flamingo" => array("SOLD", true),
        "Bunting" => array("SOLD", true),
        "Buzzard" => array("REQUEST", false),
        "Stonechat" => array("AVAILABLE", true),
        "Swallow" => array("SOLD", true),
        "Black-capped Chickadee" => array("SOLD", true),
        "Double-striped Thick-knee" => array("REQUEST", false),
        "Parakeet" => array("REQUEST", false),
        "Bobolink" => array("AVAILABLE", true),
        "Green-tailed Towhee" => array("AVAILABLE", true),
        "Warbler" => array("AVAILABLE", true),
        "Ostrich" => array("REQUEST", false),
        "Grosbeak" => array("SOLD", true),
        "Magpie" => array("REQUEST", false),
        "Babbler" => array("AVAILABLE", true),
        "Partridge" => array("AVAILABLE", true),
        "Kiwi" => array("REQUEST", false),
        "Bobwhite" => array("REQUEST", false),
        "Sandpiper" => array("REQUEST", false),
        "Oriole" => array("SOLD", true),
        "Emu" => array("SOLD", true),
        "Seagull" => array("REQUEST", false),
        "Petrel" => array("REQUEST", false),
        "Kestrel" => array("AVAILABLE", true),
        "Hairy-backed Bulbul" => array("AVAILABLE", true),
        "Parrot" => array("REQUEST", false),
        "Quail" => array("REQUEST", false),
        "Nightingale" => array("REQUEST", false),
        "White-chinned Thistletail" => array("AVAILABLE", true),
        "Tanager" => array("SOLD", true),
        "Wood-rail" => array("AVAILABLE", true),
        "Hottentot Buttonquail" => array("REQUEST", false),
        "Cassowary" => array("REQUEST", false),
        "Antstrike" => array("REQUEST", false),
        "Leaflove" => array("AVAILABLE", true),
        "Pewee" => array("REQUEST", false),
        "Vulture" => array("REQUEST", false),
        "Stilt" => array("REQUEST", false),
        "Blue-footed Booby" => array("AVAILABLE", true),
        "Blackbird" => array("SOLD", true),
        "Bluebird" => array("AVAILABLE", true),
        "Redbird" => array("SOLD", true),
        "Hummingbird" => array("SOLD", true),
        "Catbird" => array("AVAILABLE", true),
        "Mousebird" => array("AVAILABLE", true),
        "White-eared Puffbird" => array("REQUEST", false),
        "Nunbird" => array("AVAILABLE", true),
        "Friarbird" => array("REQUEST", false),
        "Lovebird" => array("REQUEST", false),
        "Thornbird" => array("AVAILABLE", true),
        "Yellow-rumped Tinkerbird " => array("AVAILABLE", true),
        "Wattlebird" => array("REQUEST", false),
        "Stitchbird" => array("AVAILABLE", true),
        "Puffin" => array("SOLD", true),
        "Duck" => array("REQUEST", false),
        "Crow" => array("REQUEST", false),
        "Snipe" => array("REQUEST", false),
        "Sugarbird" => array("AVAILABLE", true),
        "Honeyguide" => array("REQUEST", false),
        "Peregrine" => array("REQUEST", false),
        "Pelican" => array("REQUEST", false),
        "Purple-throated Cuckooshrike" => array("SOLD", true),
        "Roadrunner" => array("REQUEST", false),
        "Nutcracker" => array("REQUEST", false),
        "Kingfisher" => array("SOLD", true),
        "Reedhaunter" => array("REQUEST", false),
        "Oystrcather" => array("REQUEST", false),
        "Rockjumper" => array("RESERVED", true),
        "Groundcreeper" => array("REQUEST", false),
        "Horned Screamer" => array("REQUEST", false),
        "Meadowlark" => array("SOLD", true),
        "Nightjar" => array("REQUEST", false),
        "Jackdaw" => array("REQUEST", false),
        "Jacamar" => array("AVAILABLE", true),
        "Whooperwill" => array("REQUEST", false),
        "Spoonbill" => array("REQUEST", false),
        "Nighthawk" => array("SOLD", true),
        "Redstart" => array("SOLD", true),
        "Scarlet Ibis" => array("AVAILABLE", true),
        "Swift" => array("REQUEST", false),
        "Pigeon" => array("REQUEST", false),
        "Kite" => array("REQUEST", false),
        "Twelve-wired Bird-of-paradise" => array("REQUEST", false),
        "Rufous-headed Ground Roller" => array("AVAILABLE", true),
        "Tawny-throated Leaftosser" => array("REQUEST", false),
        "Black-cheeked Gnateater" => array("REQUEST", false),
        "Streak-necked Flycatcher" => array("REQUEST", false),
        "Purple-bearded Bee-eater" => array("SOLD", true),
        "Fan-tailed Berrypecker" => array("REQUEST", false),
        "Naked-faced Spiderhunter" => array("SOLD", true),
        "Black-bellied Seedcracker" => array("REQUEST", false),
        "Umbrellabird" => array("REQUEST", false),
        "Lorikeet" => array("SOLD", true),
        "Bufflehead" => array("REQUEST", false),
        "Hornbill" => array("REQUEST", false),
        "Mockingbird" => array("SOLD", true),
        "Manakin" => array("REQUEST", false),
        "Bittern" => array("REQUEST", false),
        "Poorwill" => array("REQUEST", false),
        "Grey Jay" => array("REQUEST", false),
        "Snowcap" => array("AVAILABLE", true),
        "Wheatear" => array("REQUEST", false),
        "Shoebill" => array("REQUEST", false),
        "Hammerkop" => array("SOLD", true),
        "Saddleback" => array("REQUEST", false),
        "Friutcrow" => array("REQUEST", false),
        "Hookbill" => array("REQUEST", false),
        "Cardinal" => array("SOLD", true),
        "Sunbird" => array("REQUEST", false),
        "Snowy Egret" => array("REQUEST", false),
        "Crake" => array("REQUEST", false),
        "Macaw" => array("REQUEST", false),
        "Woodhen" => array("REQUEST", false),
        "Barbet" => array("AVAILABLE", true),
        "Goldfinch" => array("REQUEST", false),
        "Purple Martin" => array("AVAILABLE", true),
        "Dodo" => array("REQUEST", false),
        "Osprey" => array("REQUEST", false),
        "Bullfinch" => array("AVAILABLE", true),
        "Plover" => array("REQUEST", false),
        "Whooping Crane" => array("SOLD", true),
        "Raven" => array("REQUEST", false),
        "Starling" => array("AVAILABLE", true),
        "Blue Jay" => array("SOLD", true),
        "Nuthatch" => array("REQUEST", false),
        "Falcon" => array("REQUEST", false),
        "Waxwing" => array("SOLD",true),
        "Siamese Fireback" => array("AVAILABLE", true),
        "Bareheaded Laughingthrush" => array("REQUEST", false),
        "Shearwater" => array("AVAILABLE", true),
        "Sparrowhawk" => array("AVAILABLE", true),
        "Long-toed Lapwing" => array("REQUEST", false),
        "Golden-bellied Gerrygone" => array("AVAILABLE", true),
        "Pipit" => array("AVAILABLE", true),
        "Linnet" => array("REQUEST", false),
        "Boatbill" => array("SOLD", true),
        "Junco" => array("REQUEST", false),
        "Giant Coot" => array("REQUEST", false),
        "Square-tailed Drongo" => array("AVAILABLE", true),
        "Condor" => array("REQUEST", false),
        "Toucan" => array("REQUEST", false),
        "Cuckoo" => array("REQUEST", false),
        "Turkey" => array("SOLD", true),
        "Great Crested Grebe" => array("REQUEST", false),
        "Thrush" => array("SOLD", true),
        "Teal" => array("REQUEST", false),
        "Loon" => array("REQUEST", false),
        "Tern" => array("REQUEST", false),
        "Grouse" => array("REQUEST", false),
        "Stork" => array("REQUEST", false),
        "Pheasant" => array("REQUEST", false),
        "Pink-footed Puffback" => array("REQUEST", false),
        "Bristlefront" => array("AVAILABLE", true),
        "Cormorant" => array("REQUEST", false),
        "Eagle" => array("SOLD", true),
        "Penguin" => array("REQUEST", false),
        "Red-headed Woodpecker" => array("SOLD", true),
        "Jacky Winter" => array("AVAILABLE", true),
        "Yellow-bellied Sapsucker" => array("AVAILABLE", true),
    ];
    ?>
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
                        <div class="row">
                            <div class="col-xs-12 col-md-3 center-contents">
                                <img src="<?php echo IMG_DIR . "/Songbird-45-cover.png"; ?>" width=200>
                            </div>
                            <div class="col-xs-12 col-md-9">
                                <p><b>CLEARANCE SALE: I am offering the birds I have already drawn for <s>$50</s> <span style="color: red;">$25</span> (includes tax and shipping in the US).

                                </b></p>
                                <p>
                                Below is the list of all the birds in my song "<a href="./song.php?song-id=38">Birdsong</a>".&nbsp;&nbsp;I'm hand drawing each bird
                                on the cover of one 45 rpm vinyl single.&nbsp;&nbsp;The record has "Songbird" on side A and "Birdsong" on side B.&nbsp;&nbsp;
                                If you'd be interested in purchasing one, they are <s>$50</s> <span style="color: red;">$25</span> each (includes tax and shipping in the US).&nbsp;&nbsp;Just <a href="./Contact.php">contact me</a> and we can work it out.
                                &nbsp;&nbsp;If a bird is listed as <span style="color: red;">SOLD</span> or <span style="color: red;">RESERVED</span>, 
                                that bird is already taken.&nbsp;&nbsp;Sorry.&nbsp;&nbsp;Otherwise, it's available, and you can click on the word <b>REQUEST</b> to contact me about 
                                purchasing it.&nbsp;&nbsp;You can see completed drawings by clicking on "See Image".&nbsp;&nbsp;
                                Please note that it takes me a little while to draw these, and I'm not repeating any birds so that 
                                each 45 single is unique.
                                </p>     
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
            <main id="main-content bird-list" class="col-xs-12">
                <div class="row">
                    <div class="col-xs-12">
                        <header class="presskit-section-header basic-box orange-box">
                            REQUEST Birds
                        </header>
                        
                            <?php
                            $number = 1;
                            foreach ($birds as $key => $value) {
                                echo "<div class=\"row\">";
                                echo "<div style=\"border-bottom: 1px solid grey\">";
                                $status = $value[0];
                                echo "<a name=\"${number}\">";
                                echo "<div class=\"col-xs-1 bird-number ${status}\">{$number}</div>";
                                echo "</a>";
                                echo "<div class=\"col-xs-11 col-sm-4 bird-name ${status}\">{$key}</div>";
                                echo "<div class=\"col-xs-6 col-sm-4 col-md-2 bird-availability ${status}\">";
                                if($status == "REQUEST" ){
                                    echo "<A HREF=\"mailto:contact@steveweeksmusic.com?subject=Reservation%20request%20for%20${key}&body=I%20would%20like%20to%20reserve%20${key}...\" target=\"_blank\">{$status}</A>";
                                }
                                else if($status == "AVAILABLE") {
                                    echo "<A HREF=\"mailto:contact@steveweeksmusic.com?subject=Request%20to%20buy%20${key}&body=I%20want%20the%20${key}...\" target=\"_blank\">{$status}</A>";                      
                                }
                                else {
                                    echo "<b>${status}</b>";
                                }
                                echo "</div>";

                                echo "<div class=\"col-xs-6 col-sm-3 col-md-5 bird-image ${status}\">";
                                if ($value[1]) {
                                    echo "<a href = '#${number}' onclick='showImage(${number}, \"${key}\")'>";
                                    echo "See Image <img src=\"" . IMG_DIR . "/birds/${number}-thumb.png\" height=\"30\" ></a>";                             
                                }
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";
            
                                $number++;
                            }
                            ?>
            
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 full-width-xs">
                        <header class="presskit-section-header blue-box">
                            <a name="contact">
                                Contact Info
                            </a>
                        </header>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-md-8 col-md-push-2">
                        <div class="frame-box red-frame">
                            <div class="contact">
                                <div class="email">Email: <a href="mailto:steve@steveweeksmusic.com">steve@steveweeksmusic.com</a></div>
                                <div class="email">Phone: 719-640-9986</div><br>
                            </div>
                        </div>
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
</BODY>

</HTML>