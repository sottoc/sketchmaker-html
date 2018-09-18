<?php
require 'classes/bootstrap.php';

//Some service variables
//@todo: move to class
$page_title = 'Canvas Recorder';
$current_page = 'home'; 

// cache buster
function cb ($file) {
  $dir = dirname(__FILE__) . '/';
  echo $file . '?' . filemtime($dir . $file);
}
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title><?php echo $page_title; ?></title>

  <link href="vendor/twbs/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
  <link href="vendor/fortawesome/font-awesome/css/font-awesome.css" rel="stylesheet">
  <link href="css/jquery.growl.css" rel="stylesheet">
  <link href="js/fine-uploader/fine-uploader-gallery.min.css" rel="stylesheet">
  <link href="css/select2.min.css" rel="stylesheet">
  <link href="css/select2-bootstrap4.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php cb('css/simplebar.css'); ?>" />
  <link href="css/fonts.css" rel="stylesheet">
  <link href="<?php cb('css/main.css'); ?>" rel="stylesheet">


</head>

<body class="theApp">
<div id="overlay"></div>
<?php
include_once('includes/navbar.php');
?>

<main role="main" class="appContainer fw-1280">
  <div class="row pt-2">
    <div class="col">
      <div class="clearfix mb-2 pt-1">
        <button id="newProject" class="btn btn-primary pull-left">New Project</button>
        <div class="pull-right"><span class="sr16by9">[16:9]</span><span class="sr9by16">[9:16]</span></div>
        <h1 id="projectName" style="font-size: 2rem; margin-left: 140px; margin-bottom:0; margin-top: -4px;"></h1>
      </div>
      <hr>
      <ul class="nav nav-tabs nav-justified" id="designTabsNav" role="tablist">
        <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#tab-design" role="tab">
            Design
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" data-toggle="tab" href="#tab-images" role="tab">
            Add Images
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#tab-text" role="tab">
            Add Text
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link bg-grey" data-toggle="tab" href="#tab-hand" role="tab">
            Change Hand
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#tab-effects" role="tab">
            Effects
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#tab-background" role="tab">
            Background
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#tab-music" role="tab">
            Music
          </a>
        </li>
      </ul>
<!-- // Design Tab -->
      <div class="tab-content" id="designTabsContent">
        <div class="tab-pane" id="tab-design" role="tabpanel" data-simplebar>
          <div class="list-group designList appSortable" id="designList"></div>
<?php /*
       * Temporarry commented
       * @author n.z@software-art.com
          <div class="progressBar pt-4" id="renderProgressBar">
            <blockquote class="blockquote">
              <p class="mb-0">Render Progress</p>
            </blockquote>
            <div class="progress">
              <div class="progress-bar w-75" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
          </div>
*/ ?>          
        </div>
<!-- // Add Image Tab -->        
        <div class="tab-pane show active flex-wrap root" id="tab-images" role="tabpanel">
            <ul class="list-group collapsed" id="imagesBrowserComponent">
                <li class="list-group-item border-top-0 border-bottom-0 rounded-0 row m-0 ">
                    <div id="uploader"></div>
                </li>
                <li class="list-group-item border-top-0 row m-0 d-flex justify-content-between align-items-start">
                    <div id="imagesDirectoryWrap" class="loading">
                        <div class="d-flex flex-row mt-2">
                            <div id="imagesDirectoryTabsNavWrap" data-simplebar>
                                <ul class="nav nav-tabs nav-tabs--vertical nav-tabs--left" role="navigation" id="imagesDirectoryList"></ul>
                            </div>
                            <div id="imagesDirectoryTabsWrap" data-simplebar style="width: 100%;" >
                                <div id="imagesDirectoryTabs"  class="tab-content" style="width: 100%;"  ></div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
<!-- // Text Tab -->        
        <div class="tab-pane" id="tab-text" role="tabpanel">

          <div class="form-row">
            <div class="form-group col-md-10">
              <label for="thisIsText">Text</label>
              <input type="text" class="form-control" id="thisIsText" placeholder="Your text" maxlength="100">
              <input type="hidden" class="form-control" id="textKonvaId" value="">
            </div>
            <div class="form-group col-md-2">
              <label for="fontSize">Font size</label>
              <select id="fontSize" class="form-control">
                <option value="8" >8</option>
                <option value="10">10</option>
                <option value="12">12</option>
                <option value="14">14</option>
                <option value="16">16</option>
                <option value="18">18</option>
                <option value="20">20</option>
                <option value="24">24</option>
                <option value="28">28</option>
                <option value="32">32</option>
                <option value="48">48</option>
                <option selected value="64">64</option>
                <option value="72">72</option>
                <option value="80">80</option>
                <option value="96">96</option>
                <option value="100">100</option>
                <option value="128">128</option>
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-8">
              <label for="typeFace">Type Face</label>
              <select id="typeFace" class="form-control">
<!--
                <optgroup label="Common Fonts">
                    <option class="common" value="Arial">Arial</option>
                    <option class="common" value="Times New Roman">Times New Roman</option>
                    <option class="common" value="Verdana" >Verdana</option>
                </optgroup>
                <optgroup label="Google Fonts">
                    <!-- Static list of fonts to use for a first time -->
<!--
                    <option value="ABeeZee">ABeeZee</option><option value="Abel">Abel</option><option value="Abhaya Libre">Abhaya Libre</option><option value="Abril Fatface">Abril Fatface</option><option value="Aclonica">Aclonica</option><option value="Acme">Acme</option><option value="Actor">Actor</option><option value="Adamina">Adamina</option><option value="Advent Pro">Advent Pro</option><option value="Aguafina Script">Aguafina Script</option><option value="Akronim">Akronim</option><option value="Aladin">Aladin</option><option value="Aldrich">Aldrich</option><option value="Alef">Alef</option><option value="Alegreya">Alegreya</option><option value="Alegreya SC">Alegreya SC</option><option value="Alegreya Sans">Alegreya Sans</option><option value="Alegreya Sans SC">Alegreya Sans SC</option><option value="Alex Brush">Alex Brush</option><option value="Alfa Slab One">Alfa Slab One</option><option value="Alice">Alice</option><option value="Alike">Alike</option><option value="Alike Angular">Alike Angular</option><option value="Allan">Allan</option><option value="Allerta">Allerta</option><option value="Allerta Stencil">Allerta Stencil</option><option value="Allura">Allura</option><option value="Almendra">Almendra</option><option value="Almendra Display">Almendra Display</option><option value="Almendra SC">Almendra SC</option><option value="Amarante">Amarante</option><option value="Amaranth">Amaranth</option><option value="Amatic SC">Amatic SC</option><option value="Amethysta">Amethysta</option><option value="Amiko">Amiko</option><option value="Amiri">Amiri</option><option value="Amita">Amita</option><option value="Anaheim">Anaheim</option><option value="Andada">Andada</option><option value="Andika">Andika</option><option value="Angkor">Angkor</option><option value="Annie Use Your Telescope">Annie Use Your Telescope</option><option value="Anonymous Pro">Anonymous Pro</option><option value="Antic">Antic</option><option value="Antic Didone">Antic Didone</option><option value="Antic Slab">Antic Slab</option><option value="Anton">Anton</option><option value="Arapey">Arapey</option><option value="Arbutus">Arbutus</option><option value="Arbutus Slab">Arbutus Slab</option><option value="Architects Daughter">Architects Daughter</option><option value="Archivo">Archivo</option><option value="Archivo Black">Archivo Black</option><option value="Archivo Narrow">Archivo Narrow</option><option value="Aref Ruqaa">Aref Ruqaa</option><option value="Arima Madurai">Arima Madurai</option><option value="Arimo">Arimo</option><option value="Arizonia">Arizonia</option><option value="Armata">Armata</option><option value="Arsenal">Arsenal</option><option value="Artifika">Artifika</option><option value="Arvo">Arvo</option><option value="Arya">Arya</option><option value="Asap">Asap</option><option value="Asap Condensed">Asap Condensed</option><option value="Asar">Asar</option><option value="Asset">Asset</option><option value="Assistant">Assistant</option><option value="Astloch">Astloch</option><option value="Asul">Asul</option><option value="Athiti">Athiti</option><option value="Atma">Atma</option><option value="Atomic Age">Atomic Age</option><option value="Aubrey">Aubrey</option><option value="Audiowide">Audiowide</option><option value="Autour One">Autour One</option><option value="Average">Average</option><option value="Average Sans">Average Sans</option><option value="Averia Gruesa Libre">Averia Gruesa Libre</option><option value="Averia Libre">Averia Libre</option><option value="Averia Sans Libre">Averia Sans Libre</option><option value="Averia Serif Libre">Averia Serif Libre</option><option value="Bad Script">Bad Script</option><option value="Bahiana">Bahiana</option><option value="Baloo">Baloo</option><option value="Baloo Bhai">Baloo Bhai</option><option value="Baloo Bhaijaan">Baloo Bhaijaan</option><option value="Baloo Bhaina">Baloo Bhaina</option><option value="Baloo Chettan">Baloo Chettan</option><option value="Baloo Da">Baloo Da</option><option value="Baloo Paaji">Baloo Paaji</option><option value="Baloo Tamma">Baloo Tamma</option><option value="Baloo Tammudu">Baloo Tammudu</option><option value="Baloo Thambi">Baloo Thambi</option><option value="Balthazar">Balthazar</option><option value="Bangers">Bangers</option><option value="Barlow">Barlow</option><option value="Barlow Condensed">Barlow Condensed</option><option value="Barlow Semi Condensed">Barlow Semi Condensed</option><option value="Barrio">Barrio</option><option value="Basic">Basic</option><option value="Battambang">Battambang</option><option value="Baumans">Baumans</option><option value="Bayon">Bayon</option><option value="Belgrano">Belgrano</option><option value="Bellefair">Bellefair</option><option value="Belleza">Belleza</option><option value="BenchNine">BenchNine</option><option value="Bentham">Bentham</option><option value="Berkshire Swash">Berkshire Swash</option><option value="Bevan">Bevan</option><option value="Bigelow Rules">Bigelow Rules</option><option value="Bigshot One">Bigshot One</option><option value="Bilbo">Bilbo</option><option value="Bilbo Swash Caps">Bilbo Swash Caps</option><option value="BioRhyme">BioRhyme</option><option value="BioRhyme Expanded">BioRhyme Expanded</option><option value="Biryani">Biryani</option><option value="Bitter">Bitter</option><option value="Black Ops One">Black Ops One</option><option value="Bokor">Bokor</option><option value="Bonbon">Bonbon</option><option value="Boogaloo">Boogaloo</option><option value="Bowlby One">Bowlby One</option><option value="Bowlby One SC">Bowlby One SC</option><option value="Brawler">Brawler</option><option value="Bree Serif">Bree Serif</option><option value="Bubblegum Sans">Bubblegum Sans</option><option value="Bubbler One">Bubbler One</option><option value="Buda">Buda</option><option value="Buenard">Buenard</option><option value="Bungee">Bungee</option><option value="Bungee Hairline">Bungee Hairline</option><option value="Bungee Inline">Bungee Inline</option><option value="Bungee Outline">Bungee Outline</option><option value="Bungee Shade">Bungee Shade</option><option value="Butcherman">Butcherman</option><option value="Butterfly Kids">Butterfly Kids</option><option value="Cabin">Cabin</option><option value="Cabin Condensed">Cabin Condensed</option><option value="Cabin Sketch">Cabin Sketch</option><option value="Caesar Dressing">Caesar Dressing</option><option value="Cagliostro">Cagliostro</option><option value="Cairo">Cairo</option><option value="Calligraffitti">Calligraffitti</option><option value="Cambay">Cambay</option><option value="Cambo">Cambo</option><option value="Candal">Candal</option><option value="Cantarell">Cantarell</option><option value="Cantata One">Cantata One</option><option value="Cantora One">Cantora One</option><option value="Capriola">Capriola</option><option value="Cardo">Cardo</option><option value="Carme">Carme</option><option value="Carrois Gothic">Carrois Gothic</option><option value="Carrois Gothic SC">Carrois Gothic SC</option><option value="Carter One">Carter One</option><option value="Catamaran">Catamaran</option><option value="Caudex">Caudex</option><option value="Caveat">Caveat</option><option value="Caveat Brush">Caveat Brush</option><option value="Cedarville Cursive">Cedarville Cursive</option><option value="Ceviche One">Ceviche One</option><option value="Changa">Changa</option><option value="Changa One">Changa One</option><option value="Chango">Chango</option><option value="Chathura">Chathura</option><option value="Chau Philomene One">Chau Philomene One</option><option value="Chela One">Chela One</option><option value="Chelsea Market">Chelsea Market</option><option value="Chenla">Chenla</option><option value="Cherry Cream Soda">Cherry Cream Soda</option><option value="Cherry Swash">Cherry Swash</option><option value="Chewy">Chewy</option><option value="Chicle">Chicle</option><option value="Chivo">Chivo</option><option value="Chonburi">Chonburi</option><option value="Cinzel">Cinzel</option><option value="Cinzel Decorative">Cinzel Decorative</option><option value="Clicker Script">Clicker Script</option><option value="Coda">Coda</option><option value="Coda Caption">Coda Caption</option><option value="Codystar">Codystar</option><option value="Coiny">Coiny</option><option value="Combo">Combo</option><option value="Comfortaa">Comfortaa</option><option value="Coming Soon">Coming Soon</option><option value="Concert One">Concert One</option><option value="Condiment">Condiment</option><option value="Content">Content</option><option value="Contrail One">Contrail One</option><option value="Convergence">Convergence</option><option value="Cookie">Cookie</option><option value="Copse">Copse</option><option value="Corben">Corben</option><option value="Cormorant">Cormorant</option><option value="Cormorant Garamond">Cormorant Garamond</option><option value="Cormorant Infant">Cormorant Infant</option><option value="Cormorant SC">Cormorant SC</option><option value="Cormorant Unicase">Cormorant Unicase</option><option value="Cormorant Upright">Cormorant Upright</option><option value="Courgette">Courgette</option><option value="Cousine">Cousine</option><option value="Coustard">Coustard</option><option value="Covered By Your Grace">Covered By Your Grace</option><option value="Crafty Girls">Crafty Girls</option><option value="Creepster">Creepster</option><option value="Crete Round">Crete Round</option><option value="Crimson Text">Crimson Text</option><option value="Croissant One">Croissant One</option><option value="Crushed">Crushed</option><option value="Cuprum">Cuprum</option><option value="Cutive">Cutive</option><option value="Cutive Mono">Cutive Mono</option><option value="Damion">Damion</option><option value="Dancing Script">Dancing Script</option><option value="Dangrek">Dangrek</option><option value="David Libre">David Libre</option><option value="Dawning of a New Day">Dawning of a New Day</option><option value="Days One">Days One</option><option value="Dekko">Dekko</option><option value="Delius">Delius</option><option value="Delius Swash Caps">Delius Swash Caps</option><option value="Delius Unicase">Delius Unicase</option><option value="Della Respira">Della Respira</option><option value="Denk One">Denk One</option><option value="Devonshire">Devonshire</option><option value="Dhurjati">Dhurjati</option><option value="Didact Gothic">Didact Gothic</option><option value="Diplomata">Diplomata</option><option value="Diplomata SC">Diplomata SC</option><option value="Domine">Domine</option><option value="Donegal One">Donegal One</option><option value="Doppio One">Doppio One</option><option value="Dorsa">Dorsa</option><option value="Dosis">Dosis</option><option value="Dr Sugiyama">Dr Sugiyama</option><option value="Duru Sans">Duru Sans</option><option value="Dynalight">Dynalight</option><option value="EB Garamond">EB Garamond</option><option value="Eagle Lake">Eagle Lake</option><option value="Eater">Eater</option><option value="Economica">Economica</option><option value="Eczar">Eczar</option><option value="El Messiri">El Messiri</option><option value="Electrolize">Electrolize</option><option value="Elsie">Elsie</option><option value="Elsie Swash Caps">Elsie Swash Caps</option><option value="Emblema One">Emblema One</option><option value="Emilys Candy">Emilys Candy</option><option value="Encode Sans">Encode Sans</option><option value="Encode Sans Condensed">Encode Sans Condensed</option><option value="Encode Sans Expanded">Encode Sans Expanded</option><option value="Encode Sans Semi Condensed">Encode Sans Semi Condensed</option><option value="Encode Sans Semi Expanded">Encode Sans Semi Expanded</option><option value="Engagement">Engagement</option><option value="Englebert">Englebert</option><option value="Enriqueta">Enriqueta</option><option value="Erica One">Erica One</option><option value="Esteban">Esteban</option><option value="Euphoria Script">Euphoria Script</option><option value="Ewert">Ewert</option><option value="Exo">Exo</option><option value="Exo 2">Exo 2</option><option value="Expletus Sans">Expletus Sans</option><option value="Fanwood Text">Fanwood Text</option><option value="Farsan">Farsan</option><option value="Fascinate">Fascinate</option><option value="Fascinate Inline">Fascinate Inline</option><option value="Faster One">Faster One</option><option value="Fasthand">Fasthand</option><option value="Fauna One">Fauna One</option><option value="Faustina">Faustina</option><option value="Federant">Federant</option><option value="Federo">Federo</option><option value="Felipa">Felipa</option><option value="Fenix">Fenix</option><option value="Finger Paint">Finger Paint</option><option value="Fira Mono">Fira Mono</option><option value="Fira Sans">Fira Sans</option><option value="Fira Sans Condensed">Fira Sans Condensed</option><option value="Fira Sans Extra Condensed">Fira Sans Extra Condensed</option><option value="Fjalla One">Fjalla One</option><option value="Fjord One">Fjord One</option><option value="Flamenco">Flamenco</option><option value="Flavors">Flavors</option><option value="Fondamento">Fondamento</option><option value="Fontdiner Swanky">Fontdiner Swanky</option><option value="Forum">Forum</option><option value="Francois One">Francois One</option><option value="Frank Ruhl Libre">Frank Ruhl Libre</option><option value="Freckle Face">Freckle Face</option><option value="Fredericka the Great">Fredericka the Great</option><option value="Fredoka One">Fredoka One</option><option value="Freehand">Freehand</option><option value="Fresca">Fresca</option><option value="Frijole">Frijole</option><option value="Fruktur">Fruktur</option><option value="Fugaz One">Fugaz One</option><option value="GFS Didot">GFS Didot</option><option value="GFS Neohellenic">GFS Neohellenic</option><option value="Gabriela">Gabriela</option><option value="Gafata">Gafata</option><option value="Galada">Galada</option><option value="Galdeano">Galdeano</option><option value="Galindo">Galindo</option><option value="Gentium Basic">Gentium Basic</option><option value="Gentium Book Basic">Gentium Book Basic</option><option value="Geo">Geo</option><option value="Geostar">Geostar</option><option value="Geostar Fill">Geostar Fill</option><option value="Germania One">Germania One</option><option value="Gidugu">Gidugu</option><option value="Gilda Display">Gilda Display</option><option value="Give You Glory">Give You Glory</option><option value="Glass Antiqua">Glass Antiqua</option><option value="Glegoo">Glegoo</option><option value="Gloria Hallelujah">Gloria Hallelujah</option><option value="Goblin One">Goblin One</option><option value="Gochi Hand">Gochi Hand</option><option value="Gorditas">Gorditas</option><option value="Goudy Bookletter 1911">Goudy Bookletter 1911</option><option value="Graduate">Graduate</option><option value="Grand Hotel">Grand Hotel</option><option value="Gravitas One">Gravitas One</option><option value="Great Vibes">Great Vibes</option><option value="Griffy">Griffy</option><option value="Gruppo">Gruppo</option><option value="Gudea">Gudea</option><option value="Gurajada">Gurajada</option><option value="Habibi">Habibi</option><option value="Halant">Halant</option><option value="Hammersmith One">Hammersmith One</option><option value="Hanalei">Hanalei</option><option value="Hanalei Fill">Hanalei Fill</option><option value="Handlee">Handlee</option><option value="Hanuman">Hanuman</option><option value="Happy Monkey">Happy Monkey</option><option value="Harmattan">Harmattan</option><option value="Headland One">Headland One</option><option value="Heebo">Heebo</option><option value="Henny Penny">Henny Penny</option><option value="Herr Von Muellerhoff">Herr Von Muellerhoff</option><option value="Hind">Hind</option><option value="Hind Guntur">Hind Guntur</option><option value="Hind Madurai">Hind Madurai</option><option value="Hind Siliguri">Hind Siliguri</option><option value="Hind Vadodara">Hind Vadodara</option><option value="Holtwood One SC">Holtwood One SC</option><option value="Homemade Apple">Homemade Apple</option><option value="Homenaje">Homenaje</option><option value="IBM Plex Mono">IBM Plex Mono</option><option value="IBM Plex Sans">IBM Plex Sans</option><option value="IBM Plex Sans Condensed">IBM Plex Sans Condensed</option><option value="IBM Plex Serif">IBM Plex Serif</option><option value="IM Fell DW Pica">IM Fell DW Pica</option><option value="IM Fell DW Pica SC">IM Fell DW Pica SC</option><option value="IM Fell Double Pica">IM Fell Double Pica</option><option value="IM Fell Double Pica SC">IM Fell Double Pica SC</option><option value="IM Fell English">IM Fell English</option><option value="IM Fell English SC">IM Fell English SC</option><option value="IM Fell French Canon">IM Fell French Canon</option><option value="IM Fell French Canon SC">IM Fell French Canon SC</option><option value="IM Fell Great Primer">IM Fell Great Primer</option><option value="IM Fell Great Primer SC">IM Fell Great Primer SC</option><option value="Iceberg">Iceberg</option><option value="Iceland">Iceland</option><option value="Imprima">Imprima</option><option value="Inconsolata">Inconsolata</option><option value="Inder">Inder</option><option value="Indie Flower">Indie Flower</option><option value="Inika">Inika</option><option value="Inknut Antiqua">Inknut Antiqua</option><option value="Irish Grover">Irish Grover</option><option value="Istok Web">Istok Web</option><option value="Italiana">Italiana</option><option value="Italianno">Italianno</option><option value="Itim">Itim</option><option value="Jacques Francois">Jacques Francois</option><option value="Jacques Francois Shadow">Jacques Francois Shadow</option><option value="Jaldi">Jaldi</option><option value="Jim Nightshade">Jim Nightshade</option><option value="Jockey One">Jockey One</option><option value="Jolly Lodger">Jolly Lodger</option><option value="Jomhuria">Jomhuria</option><option value="Josefin Sans">Josefin Sans</option><option value="Josefin Slab">Josefin Slab</option><option value="Joti One">Joti One</option><option value="Judson">Judson</option><option value="Julee">Julee</option><option value="Julius Sans One">Julius Sans One</option><option value="Junge">Junge</option><option value="Jura">Jura</option><option value="Just Another Hand">Just Another Hand</option><option value="Just Me Again Down Here">Just Me Again Down Here</option><option value="Kadwa">Kadwa</option><option value="Kalam">Kalam</option><option value="Kameron">Kameron</option><option value="Kanit">Kanit</option><option value="Kantumruy">Kantumruy</option><option value="Karla">Karla</option><option value="Karma">Karma</option><option value="Katibeh">Katibeh</option><option value="Kaushan Script">Kaushan Script</option><option value="Kavivanar">Kavivanar</option><option value="Kavoon">Kavoon</option><option value="Kdam Thmor">Kdam Thmor</option><option value="Keania One">Keania One</option><option value="Kelly Slab">Kelly Slab</option><option value="Kenia">Kenia</option><option value="Khand">Khand</option><option value="Khmer">Khmer</option><option value="Khula">Khula</option><option value="Kite One">Kite One</option><option value="Knewave">Knewave</option><option value="Kotta One">Kotta One</option><option value="Koulen">Koulen</option><option value="Kranky">Kranky</option><option value="Kreon">Kreon</option><option value="Kristi">Kristi</option><option value="Krona One">Krona One</option><option value="Kumar One">Kumar One</option><option value="Kumar One Outline">Kumar One Outline</option><option value="Kurale">Kurale</option><option value="La Belle Aurore">La Belle Aurore</option><option value="Laila">Laila</option><option value="Lakki Reddy">Lakki Reddy</option><option value="Lalezar">Lalezar</option><option value="Lancelot">Lancelot</option><option value="Lateef">Lateef</option><option value="Lato">Lato</option><option value="League Script">League Script</option><option value="Leckerli One">Leckerli One</option><option value="Ledger">Ledger</option><option value="Lekton">Lekton</option><option value="Lemon">Lemon</option><option value="Lemonada">Lemonada</option><option value="Libre Barcode 128">Libre Barcode 128</option><option value="Libre Barcode 128 Text">Libre Barcode 128 Text</option><option value="Libre Barcode 39">Libre Barcode 39</option><option value="Libre Barcode 39 Extended">Libre Barcode 39 Extended</option><option value="Libre Barcode 39 Extended Text">Libre Barcode 39 Extended Text</option><option value="Libre Barcode 39 Text">Libre Barcode 39 Text</option><option value="Libre Baskerville">Libre Baskerville</option><option value="Libre Franklin">Libre Franklin</option><option value="Life Savers">Life Savers</option><option value="Lilita One">Lilita One</option><option value="Lily Script One">Lily Script One</option><option value="Limelight">Limelight</option><option value="Linden Hill">Linden Hill</option><option value="Lobster">Lobster</option><option value="Lobster Two">Lobster Two</option><option value="Londrina Outline">Londrina Outline</option><option value="Londrina Shadow">Londrina Shadow</option><option value="Londrina Sketch">Londrina Sketch</option><option value="Londrina Solid">Londrina Solid</option><option value="Lora">Lora</option><option value="Love Ya Like A Sister">Love Ya Like A Sister</option><option value="Loved by the King">Loved by the King</option><option value="Lovers Quarrel">Lovers Quarrel</option><option value="Luckiest Guy">Luckiest Guy</option><option value="Lusitana">Lusitana</option><option value="Lustria">Lustria</option><option value="Macondo">Macondo</option><option value="Macondo Swash Caps">Macondo Swash Caps</option><option value="Mada">Mada</option><option value="Magra">Magra</option><option value="Maiden Orange">Maiden Orange</option><option value="Maitree">Maitree</option><option value="Mako">Mako</option><option value="Mallanna">Mallanna</option><option value="Mandali">Mandali</option><option value="Manuale">Manuale</option><option value="Marcellus">Marcellus</option><option value="Marcellus SC">Marcellus SC</option><option value="Marck Script">Marck Script</option><option value="Margarine">Margarine</option><option value="Marko One">Marko One</option><option value="Marmelad">Marmelad</option><option value="Martel">Martel</option><option value="Martel Sans">Martel Sans</option><option value="Marvel">Marvel</option><option value="Mate">Mate</option><option value="Mate SC">Mate SC</option><option value="Maven Pro">Maven Pro</option><option value="McLaren">McLaren</option><option value="Meddon">Meddon</option><option value="MedievalSharp">MedievalSharp</option><option value="Medula One">Medula One</option><option value="Meera Inimai">Meera Inimai</option><option value="Megrim">Megrim</option><option value="Meie Script">Meie Script</option><option value="Merienda">Merienda</option><option value="Merienda One">Merienda One</option><option value="Merriweather">Merriweather</option><option value="Merriweather Sans">Merriweather Sans</option><option value="Metal">Metal</option><option value="Metal Mania">Metal Mania</option><option value="Metamorphous">Metamorphous</option><option value="Metrophobic">Metrophobic</option><option value="Michroma">Michroma</option><option value="Milonga">Milonga</option><option value="Miltonian">Miltonian</option><option value="Miltonian Tattoo">Miltonian Tattoo</option><option value="Mina">Mina</option><option value="Miniver">Miniver</option><option value="Miriam Libre">Miriam Libre</option><option value="Mirza">Mirza</option><option value="Miss Fajardose">Miss Fajardose</option><option value="Mitr">Mitr</option><option value="Modak">Modak</option><option value="Modern Antiqua">Modern Antiqua</option><option value="Mogra">Mogra</option><option value="Molengo">Molengo</option><option value="Molle">Molle</option><option value="Monda">Monda</option><option value="Monofett">Monofett</option><option value="Monoton">Monoton</option><option value="Monsieur La Doulaise">Monsieur La Doulaise</option><option value="Montaga">Montaga</option><option value="Montez">Montez</option><option value="Montserrat">Montserrat</option><option value="Montserrat Alternates">Montserrat Alternates</option><option value="Montserrat Subrayada">Montserrat Subrayada</option><option value="Moul">Moul</option><option value="Moulpali">Moulpali</option><option value="Mountains of Christmas">Mountains of Christmas</option><option value="Mouse Memoirs">Mouse Memoirs</option><option value="Mr Bedfort">Mr Bedfort</option><option value="Mr Dafoe">Mr Dafoe</option><option value="Mr De Haviland">Mr De Haviland</option><option value="Mrs Saint Delafield">Mrs Saint Delafield</option><option value="Mrs Sheppards">Mrs Sheppards</option><option value="Mukta">Mukta</option><option value="Mukta Mahee">Mukta Mahee</option><option value="Mukta Malar">Mukta Malar</option><option value="Mukta Vaani">Mukta Vaani</option><option value="Muli">Muli</option><option value="Mystery Quest">Mystery Quest</option><option value="NTR">NTR</option><option value="Nanum Brush Script">Nanum Brush Script</option><option value="Nanum Gothic">Nanum Gothic</option><option value="Nanum Gothic Coding">Nanum Gothic Coding</option><option value="Nanum Myeongjo">Nanum Myeongjo</option><option value="Nanum Pen Script">Nanum Pen Script</option><option value="Neucha">Neucha</option><option value="Neuton">Neuton</option><option value="New Rocker">New Rocker</option><option value="News Cycle">News Cycle</option><option value="Niconne">Niconne</option><option value="Nixie One">Nixie One</option><option value="Nobile">Nobile</option><option value="Nokora">Nokora</option><option value="Norican">Norican</option><option value="Nosifer">Nosifer</option><option value="Nothing You Could Do">Nothing You Could Do</option><option value="Noticia Text">Noticia Text</option><option value="Noto Sans">Noto Sans</option><option value="Noto Serif">Noto Serif</option><option value="Nova Cut">Nova Cut</option><option value="Nova Flat">Nova Flat</option><option value="Nova Mono">Nova Mono</option><option value="Nova Oval">Nova Oval</option><option value="Nova Round">Nova Round</option><option value="Nova Script">Nova Script</option><option value="Nova Slim">Nova Slim</option><option value="Nova Square">Nova Square</option><option value="Numans">Numans</option><option value="Nunito">Nunito</option><option value="Nunito Sans">Nunito Sans</option><option value="Odor Mean Chey">Odor Mean Chey</option><option value="Offside">Offside</option><option value="Old Standard TT">Old Standard TT</option><option value="Oldenburg">Oldenburg</option><option value="Oleo Script">Oleo Script</option><option value="Oleo Script Swash Caps">Oleo Script Swash Caps</option><option value="Open Sans">Open Sans</option><option value="Open Sans Condensed">Open Sans Condensed</option><option value="Oranienbaum">Oranienbaum</option><option value="Orbitron">Orbitron</option><option value="Oregano">Oregano</option><option value="Orienta">Orienta</option><option value="Original Surfer">Original Surfer</option><option value="Oswald">Oswald</option><option value="Over the Rainbow">Over the Rainbow</option><option value="Overlock">Overlock</option><option value="Overlock SC">Overlock SC</option><option value="Overpass">Overpass</option><option value="Overpass Mono">Overpass Mono</option><option value="Ovo">Ovo</option><option value="Oxygen">Oxygen</option><option value="Oxygen Mono">Oxygen Mono</option><option value="PT Mono">PT Mono</option><option value="PT Sans">PT Sans</option><option value="PT Sans Caption">PT Sans Caption</option><option value="PT Sans Narrow">PT Sans Narrow</option><option value="PT Serif">PT Serif</option><option value="PT Serif Caption">PT Serif Caption</option><option value="Pacifico">Pacifico</option><option value="Padauk">Padauk</option><option value="Palanquin">Palanquin</option><option value="Palanquin Dark">Palanquin Dark</option><option value="Pangolin">Pangolin</option><option value="Paprika">Paprika</option><option value="Parisienne">Parisienne</option><option value="Passero One">Passero One</option><option value="Passion One">Passion One</option><option value="Pathway Gothic One">Pathway Gothic One</option><option value="Patrick Hand">Patrick Hand</option><option value="Patrick Hand SC">Patrick Hand SC</option><option value="Pattaya">Pattaya</option><option value="Patua One">Patua One</option><option value="Pavanam">Pavanam</option><option value="Paytone One">Paytone One</option><option value="Peddana">Peddana</option><option value="Peralta">Peralta</option><option value="Permanent Marker">Permanent Marker</option><option value="Petit Formal Script">Petit Formal Script</option><option value="Petrona">Petrona</option><option value="Philosopher">Philosopher</option><option value="Piedra">Piedra</option><option value="Pinyon Script">Pinyon Script</option><option value="Pirata One">Pirata One</option><option value="Plaster">Plaster</option><option value="Play">Play</option><option value="Playball">Playball</option><option value="Playfair Display">Playfair Display</option><option value="Playfair Display SC">Playfair Display SC</option><option value="Podkova">Podkova</option><option value="Poiret One">Poiret One</option><option value="Poller One">Poller One</option><option value="Poly">Poly</option><option value="Pompiere">Pompiere</option><option value="Pontano Sans">Pontano Sans</option><option value="Poppins">Poppins</option><option value="Port Lligat Sans">Port Lligat Sans</option><option value="Port Lligat Slab">Port Lligat Slab</option><option value="Pragati Narrow">Pragati Narrow</option><option value="Prata">Prata</option><option value="Preahvihear">Preahvihear</option><option value="Press Start 2P">Press Start 2P</option><option value="Pridi">Pridi</option><option value="Princess Sofia">Princess Sofia</option><option value="Prociono">Prociono</option><option value="Prompt">Prompt</option><option value="Prosto One">Prosto One</option><option value="Proza Libre">Proza Libre</option><option value="Puritan">Puritan</option><option value="Purple Purse">Purple Purse</option><option value="Quando">Quando</option><option value="Quantico">Quantico</option><option value="Quattrocento">Quattrocento</option><option value="Quattrocento Sans">Quattrocento Sans</option><option value="Questrial">Questrial</option><option value="Quicksand">Quicksand</option><option value="Quintessential">Quintessential</option><option value="Qwigley">Qwigley</option><option value="Racing Sans One">Racing Sans One</option><option value="Radley">Radley</option><option value="Rajdhani">Rajdhani</option><option value="Rakkas">Rakkas</option><option value="Raleway">Raleway</option><option value="Raleway Dots">Raleway Dots</option><option value="Ramabhadra">Ramabhadra</option><option value="Ramaraja">Ramaraja</option><option value="Rambla">Rambla</option><option value="Rammetto One">Rammetto One</option><option value="Ranchers">Ranchers</option><option value="Rancho">Rancho</option><option value="Ranga">Ranga</option><option value="Rasa">Rasa</option><option value="Rationale">Rationale</option><option value="Ravi Prakash">Ravi Prakash</option><option value="Redressed">Redressed</option><option value="Reem Kufi">Reem Kufi</option><option value="Reenie Beanie">Reenie Beanie</option><option value="Revalia">Revalia</option><option value="Rhodium Libre">Rhodium Libre</option><option value="Ribeye">Ribeye</option><option value="Ribeye Marrow">Ribeye Marrow</option><option value="Righteous">Righteous</option><option value="Risque">Risque</option><option value="Roboto">Roboto</option><option value="Roboto Condensed">Roboto Condensed</option><option value="Roboto Mono">Roboto Mono</option><option value="Roboto Slab">Roboto Slab</option><option value="Rochester">Rochester</option><option value="Rock Salt">Rock Salt</option><option value="Rokkitt">Rokkitt</option><option value="Romanesco">Romanesco</option><option value="Ropa Sans">Ropa Sans</option><option value="Rosario">Rosario</option><option value="Rosarivo">Rosarivo</option><option value="Rouge Script">Rouge Script</option><option value="Rozha One">Rozha One</option><option value="Rubik">Rubik</option><option value="Rubik Mono One">Rubik Mono One</option><option value="Ruda">Ruda</option><option value="Rufina">Rufina</option><option value="Ruge Boogie">Ruge Boogie</option><option value="Ruluko">Ruluko</option><option value="Rum Raisin">Rum Raisin</option><option value="Ruslan Display">Ruslan Display</option><option value="Russo One">Russo One</option><option value="Ruthie">Ruthie</option><option value="Rye">Rye</option><option value="Sacramento">Sacramento</option><option value="Sahitya">Sahitya</option><option value="Sail">Sail</option><option value="Saira">Saira</option><option value="Saira Condensed">Saira Condensed</option><option value="Saira Extra Condensed">Saira Extra Condensed</option><option value="Saira Semi Condensed">Saira Semi Condensed</option><option value="Salsa">Salsa</option><option value="Sanchez">Sanchez</option><option value="Sancreek">Sancreek</option><option value="Sansita">Sansita</option><option value="Sarala">Sarala</option><option value="Sarina">Sarina</option><option value="Sarpanch">Sarpanch</option><option value="Satisfy">Satisfy</option><option value="Scada">Scada</option><option value="Scheherazade">Scheherazade</option><option value="Schoolbell">Schoolbell</option><option value="Scope One">Scope One</option><option value="Seaweed Script">Seaweed Script</option><option value="Secular One">Secular One</option><option value="Sedgwick Ave">Sedgwick Ave</option><option value="Sedgwick Ave Display">Sedgwick Ave Display</option><option value="Sevillana">Sevillana</option><option value="Seymour One">Seymour One</option><option value="Shadows Into Light">Shadows Into Light</option><option value="Shadows Into Light Two">Shadows Into Light Two</option><option value="Shanti">Shanti</option><option value="Share">Share</option><option value="Share Tech">Share Tech</option><option value="Share Tech Mono">Share Tech Mono</option><option value="Shojumaru">Shojumaru</option><option value="Short Stack">Short Stack</option><option value="Shrikhand">Shrikhand</option><option value="Siemreap">Siemreap</option><option value="Sigmar One">Sigmar One</option><option value="Signika">Signika</option><option value="Signika Negative">Signika Negative</option><option value="Simonetta">Simonetta</option><option value="Sintony">Sintony</option><option value="Sirin Stencil">Sirin Stencil</option><option value="Six Caps">Six Caps</option><option value="Skranji">Skranji</option><option value="Slabo 13px">Slabo 13px</option><option value="Slabo 27px">Slabo 27px</option><option value="Slackey">Slackey</option><option value="Smokum">Smokum</option><option value="Smythe">Smythe</option><option value="Sniglet">Sniglet</option><option value="Snippet">Snippet</option><option value="Snowburst One">Snowburst One</option><option value="Sofadi One">Sofadi One</option><option value="Sofia">Sofia</option><option value="Sonsie One">Sonsie One</option><option value="Sorts Mill Goudy">Sorts Mill Goudy</option><option value="Source Code Pro">Source Code Pro</option><option value="Source Sans Pro">Source Sans Pro</option><option value="Source Serif Pro">Source Serif Pro</option><option value="Space Mono">Space Mono</option><option value="Special Elite">Special Elite</option><option value="Spectral">Spectral</option><option value="Spectral SC">Spectral SC</option><option value="Spicy Rice">Spicy Rice</option><option value="Spinnaker">Spinnaker</option><option value="Spirax">Spirax</option><option value="Squada One">Squada One</option><option value="Sree Krushnadevaraya">Sree Krushnadevaraya</option><option value="Sriracha">Sriracha</option><option value="Stalemate">Stalemate</option><option value="Stalinist One">Stalinist One</option><option value="Stardos Stencil">Stardos Stencil</option><option value="Stint Ultra Condensed">Stint Ultra Condensed</option><option value="Stint Ultra Expanded">Stint Ultra Expanded</option><option value="Stoke">Stoke</option><option value="Strait">Strait</option><option value="Sue Ellen Francisco">Sue Ellen Francisco</option><option value="Suez One">Suez One</option><option value="Sumana">Sumana</option><option value="Sunshiney">Sunshiney</option><option value="Supermercado One">Supermercado One</option><option value="Sura">Sura</option><option value="Suranna">Suranna</option><option value="Suravaram">Suravaram</option><option value="Suwannaphum">Suwannaphum</option><option value="Swanky and Moo Moo">Swanky and Moo Moo</option><option value="Syncopate">Syncopate</option><option value="Tangerine">Tangerine</option><option value="Taprom">Taprom</option><option value="Tauri">Tauri</option><option value="Taviraj">Taviraj</option><option value="Teko">Teko</option><option value="Telex">Telex</option><option value="Tenali Ramakrishna">Tenali Ramakrishna</option><option value="Tenor Sans">Tenor Sans</option><option value="Text Me One">Text Me One</option><option value="The Girl Next Door">The Girl Next Door</option><option value="Tienne">Tienne</option><option value="Tillana">Tillana</option><option value="Timmana">Timmana</option><option value="Tinos">Tinos</option><option value="Titan One">Titan One</option><option value="Titillium Web">Titillium Web</option><option value="Trade Winds">Trade Winds</option><option value="Trirong">Trirong</option><option value="Trocchi">Trocchi</option><option value="Trochut">Trochut</option><option value="Trykker">Trykker</option><option value="Tulpen One">Tulpen One</option><option value="Ubuntu">Ubuntu</option><option value="Ubuntu Condensed">Ubuntu Condensed</option><option value="Ubuntu Mono">Ubuntu Mono</option><option value="Ultra">Ultra</option><option value="Uncial Antiqua">Uncial Antiqua</option><option value="Underdog">Underdog</option><option value="Unica One">Unica One</option><option value="UnifrakturCook">UnifrakturCook</option><option value="UnifrakturMaguntia">UnifrakturMaguntia</option><option value="Unkempt">Unkempt</option><option value="Unlock">Unlock</option><option value="Unna">Unna</option><option value="VT323">VT323</option><option value="Vampiro One">Vampiro One</option><option value="Varela">Varela</option><option value="Varela Round">Varela Round</option><option value="Vast Shadow">Vast Shadow</option><option value="Vesper Libre">Vesper Libre</option><option value="Vibur">Vibur</option><option value="Vidaloka">Vidaloka</option><option value="Viga">Viga</option><option value="Voces">Voces</option><option value="Volkhov">Volkhov</option><option value="Vollkorn">Vollkorn</option><option value="Vollkorn SC">Vollkorn SC</option><option value="Voltaire">Voltaire</option><option value="Waiting for the Sunrise">Waiting for the Sunrise</option><option value="Wallpoet">Wallpoet</option><option value="Walter Turncoat">Walter Turncoat</option><option value="Warnes">Warnes</option><option value="Wellfleet">Wellfleet</option><option value="Wendy One">Wendy One</option><option value="Wire One">Wire One</option><option value="Work Sans">Work Sans</option><option value="Yanone Kaffeesatz">Yanone Kaffeesatz</option><option value="Yantramanav">Yantramanav</option><option value="Yatra One">Yatra One</option><option value="Yellowtail">Yellowtail</option><option value="Yeseva One">Yeseva One</option><option value="Yesteryear">Yesteryear</option><option value="Yrsa">Yrsa</option><option value="Zeyada">Zeyada</option><option value="Zilla Slab">Zilla Slab</option><option value="Zilla Slab Highlight">Zilla Slab Highlight</option>                
                </optgroup>
-->
              </select>
            </div>
            <div class="form-group col-md-4">
                <label for="typeColor">Type Color</label>
                <input id="fontColor" class="form-control jscolor" title="Type Color" value="000000">
            </div>            
          </div>
          <div class="form-group">
            <button id="addText" class="btn btn-primary">Add</button>
          </div>
        </div>
        <div class="tab-pane" id="tab-hand" role="tabpanel">
          <div class="list-group">
            <div href="#" class="list-group-item list-group-item-light text-center font-weight-bold  border-top-0  rounded-0">
                Write Hands
            </div>
            <div href="#" class="list-group-item text-center">
                <div class="hand-thumb" data-type="write" data-img="/img/hand/write/hand1.png">
                    <img src="/img/hand/write/hand1.png" alt=""/>
                </div>
                <div class="hand-thumb" data-type="write" data-img="/img/hand/write/hand2.png">
                    <img src="/img/hand/write/hand2.png" alt=""/>
                </div>
            </div>
            <div href="#" class="list-group-item list-group-item-light text-center font-weight-bold">
                Drop Hands
            </div>            
            <div href="#" class="list-group-item text-center">
                <div class="hand-thumb" data-type="drop" data-img="/img/hand/drop/image_hand.png">
                    <img src="/img/hand/drop/image_hand.png" alt=""/>
                </div>
                <div class="hand-thumb" data-type="drop" data-img="/img/hand/drop/image_hand2.png">
                    <img src="/img/hand/drop/image_hand2.png" alt=""/>
                </div>
            </div>
          </div>
        </div>
        <div class="tab-pane" id="tab-effects" role="tabpanel">
            <div class="list-group">
<?php /*
                <div class="list-group-item border-top-0  rounded-0">
                    <div class="form-group">
                        <label for="slideTransitionIn" class="font-weight-bold">Slide In Transition</label>
                        <select class="form-control" id="slideTransitionIn" onchange="recorder.setSlideTransition()">
                            <option value="">- Select Transition -</option>
                            <option value="slideInUp">slideInUp</option>
                            <option value="slideInDown">slideInDown</option>
                            <option value="slideInLeft">slideInLeft</option>
                            <option value="slideInRight" selected>slideInRight</option>
                        </select>
                    </div>
                </div>
                <div class="list-group-item">
                    <div class="form-group">
                        <label for="slideTransitionOut" class="font-weight-bold" >Slide Out Transition</label>
                        <select class="form-control" id="slideTransitionOut" onchange="recorder.setSlideTransition();">
                            <option value="">- Select Transition -</option>
                            <option value="slideOutUp">slideOutUp</option>
                            <option value="slideOutDown">slideOutDown</option>
                            <option value="slideOutLeft" selected>slideOutLeft</option>
                            <option value="slideOutRight">slideOutRight</option>
                        </select>
                    </div>
                </div>
*/ ?>                
                <div class="list-group-item list-group-item-light text-center font-weight-bold  border-top-0  rounded-0">
                    Select slide effect
                </div>                
                <div class="list-group-item text-center">
                    <div class="row">
                        <div class="col-4">
                            <div class="seffect-thumb" data-slide-in="" data-slide-out="">
                                <b>none</b>
                            </div>
                            <div class="seffect-thumb active"  data-slide-in="random" data-slide-out="random">
                                <b>random</b>
                            </div>                            
                        </div>
                        <div class="col-4">
                            <div class="seffect-thumb" data-slide-in="slideInDown" data-slide-out="slideOutUp">
                                <img src="/img/icons/icon_slide_up.png" alt=""/>
                            </div>
                            <div class="seffect-thumb" data-slide-in="slideInUp" data-slide-out="slideOutDown">
                                <img src="/img/icons/icon_slide_down.png" alt=""/>
                            </div>                            
                        </div>
                        <div class="col-4">
                            <div class="seffect-thumb" data-slide-in="slideInRight" data-slide-out="slideOutLeft">
                                <img src="/img/icons/icon_slide_left.png" alt=""/>
                            </div>
                            <div class="seffect-thumb"  data-slide-in="slideInLeft" data-slide-out="slideOutRight">
                                <img src="/img/icons/icon_slide_right.png" alt=""/>
                            </div>                             
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="tab-pane root" id="tab-background" role="tabpanel">
            <div class="list-group">
                <div class="list-group-item rounded-0 border-top-0">
                    <div class="custom-control custom-radio custom-control-inline">
                      <input type="radio" id="backgroundModeSingle" name="backgroundMode" class="custom-control-input" value="single">
                      <label class="custom-control-label" for="backgroundModeSingle">Use Same Background for all the project</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                      <input type="radio" id="backgroundModePerslide" name="backgroundMode" class="custom-control-input" value="perslide">
                      <label class="custom-control-label" for="backgroundModePerslide">Use Background for each slide</label>
                    </div>               
                </div>
                <div class="list-group-item ">
                    <button class="btn btn-sm btn-danger float-right mb-1" id="removeBackground"><i class="fa fa-eraser"></i> Remove color</button>
                    <b class="mb-1 d-block">Color: </b>
                    <input class="form-control form-control-sm jscolor {onFineChange:'recorder.setBackground(this)'}" title="Background Color" value="ffffff">
                </div>
                <div class="list-group-item rounded-0 border-bottom-0">
                    <button class="btn btn-sm btn-danger float-right mb-1" id="removeBackgroundImage"><i class="fa fa-eraser"></i> Remove image</button>
                    <b class="mb-1">Background Image:</b>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="tab-music" role="tabpanel">
            <div id="audioListContainer"></div>
            <div class="list-group rounded-bottom">
                <button type="button" class="list-group-item list-group-item-action" id="uploadAudioFile">Add Audio File</button>
            </div>
        </div>
      </div>
    </div>
    <div class="col">
      <div class="text-center myToolbar" role="toolbar" aria-label="Toolbar with button groups">
        <div class="btn-group mr-2" role="group" aria-label="Zero group">
            <button type="button" class="btn btn-primary" id="openProject" title="Open Project">
              <span class="fa fa-folder-open-o"></span>
            </button>

          <button type="button" class="btn btn-primary" id="saveProject" title="Save Project">
            <span class="fa fa-save"></span>
          </button>
          <button type="button" class="btn btn-primary" id="saveProjectAs" title="Save Project As...">
            <span class="fa fa-save"></span> ...
          </button>
          <button type="button" class="btn btn-primary" id="downloadVideo" title="Download Video">
            <span class="fa fa-download"></span>
          </button>
        </div>
        <div class="btn-group mr-2" role="group" aria-label="Zero and a half group">
          <button type="button" class="btn btn-primary" id="undoButton" title="Undo">
            <span class="fa fa-undo"></span>
          </button>
        </div>
        <div class="btn-group mr-2" role="group" aria-label="First group">
          <button id="newSlide" title="New Slide" type="button" class="btn btn-secondary">
            <span class="fa fa-plus-circle"></span>
          </button>
          <button id="previewSlide" title="Preview Slide" class="btn btn-secondary" type="button">
            <span class="fa fa-play-circle"></span>
          </button>
          <button type="button" class="btn btn-secondary" id="clearSlide" title="Clear Slide">
            <span class="fa fa-eraser"></span>
          </button>
          <button type="button" id="deleteSlide" class="btn btn-secondary slideControl slideDelete" data-id="1" title="Remove Slide">
            <span class="fa fa-trash-o"></span>
          </button>
        </div>
        <div class="btn-group mr-2" role="group" aria-label="Third group">
          <button type="button" class="btn btn-secondary" title="Preview Animation" id="animationPreview">
            <span class="fa fa-play"></span>
          </button>
          <button type="button" class="btn btn-secondary" title="Stop Preview" id="animationPreviewStop">
            <span class="fa fa-stop"></span>
          </button>
        </div>
        <div class="btn-group" role="group" aria-label="Third group">
          <button type="button" class="btn btn-primary" id="recordVideo" title="Export Video">
            <span class="fa fa-video-camera"></span>
          </button>
        </div>        
      </div>

      <div id="mainCanvasContainerWrap"><div id="mainCanvasContainer" class="embed-responsive-item theCanvas"></div></div>
      <div id="slidesPreviewsContainer" class="slidesPreviewsContainer">
        <div class="inner">
            <div id="slidesPreviewsContainerSlides"></div>
            <div id="slidesPreviewsContainerAddSlide">
                <button class="action-add-new-slide btn btn-success rounded-circle"><i class="fa fa-plus"></i></button>
            </div>
        </div>
      </div>
      <div id="audioAdditionalInfo" class="text-center"></div>
      <div id="videoGenerationProgress" class="videoGenerationProgress">
        <button class="btn btn-primary btn-block"></button>
      </div>
    </div>
  </div>
</main>
<?php
include_once('includes/footer.php');
?>
<div id="newProjectDialogContent" style="display:none;">
  <form class="form" role="form">
    <div class="form-group">
      <label for="projectName">Project Name</label>
      <input type="text" class="form-control" id="projectName" name="projectName" placeholder="Project Name" value="">
    </div>
    <div class="form-group">
      <label for="screenRatio">Screen Ratio</label>
      <select class="form-control" id="screenRatio" name="screenRatio">
        <option value="16by9">16:9</option>
        <option value="9by16">9:16</option>
      </select>
    </div>
  </form>
</div>

<div id="videoExportOptionsDialog" style="display:none;">
  <form class="form" role="form">
    <div class="form-group">
      <label for="fileFormat">File Format</label>
      <select class="form-control" id="fileFormat" name="fileFormat">
        <option value="mp4">mp4</option>
        <option value="avi">avi</option>
        <option value="mkv">mkv</option>
        <option value="wmv">wmv</option>
      </select>
    </div>
    <div class="form-group">
      <label for="videoResolution">Video Resolution</label>
      <select class="form-control" id="videoResolution" name="videoResolution">
        <option value="">Please Select</option>
        <option value="640x360" class="16by9">640x360</option>
        <option value="1280x720" class="16by9 default">1280x720</option>
        <option value="1920x1080" class="16by9">1920x1080</option>
        <option value="360x640" class="9by16">360x640</option>
        <option value="720x1280" class="9by16 default">720x1280</option>
        <option value="1080x1920" class="9by16">1080x1920</option>
      </select>
    </div>
  </form>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modalAudioRecorder">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Audio Recorder</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="text-center">
        <select id="audio-in-select" class="form-control"></select>
        <input id="audio-in-level" type="range" min="0" max="100" value="100" class="hidden">
        <input type="radio" name="encoding" encoding="wav" checked="" class="hidden">
            <span id="AudioRecorderTimer" aria-hidden="true">00:00</span>
            <button id="btnStartStop"  class="btn btn-primary mt-2">Record</button>
        </div>
        </div>
    </div>
  </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="modalAddAudioItem">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Upload Audio File</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="audioItemUploader"></div>
        <div class="text-center">
            <button id="btnAttachToKonvaItem"  class="btn btn-primary mt-2 "style="display:none;">Attach</button>
        </div>
        </div>
    </div>
  </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="modalAddAudioToLibrary">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Audio File to library</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="audioLibraryUploader"></div>
      </div>
    </div>
  </div>
</div>
<div id="overlay_full"></div>
<?php include dirname(__FILE__) . '/classes/templates.php'; ?>
<script src="<?php cb('vendor/components/jquery/jquery.js'); ?>"></script>
<script src="<?php cb('vendor/twbs/bootstrap/assets/js/vendor/popper.min.js'); ?>"></script>
<script src="<?php cb('vendor/twbs/bootstrap/dist/js/bootstrap.js'); ?>"></script>
<script src="<?php cb('js/jquery.growl.js'); ?>"></script>
<script src="<?php cb('js/bootbox/src/bootbox.js'); ?>"></script>
<script src="<?php cb('js/bootbox/src/bootbox.locales.js'); ?>"></script>
<script src="<?php cb('js/konva.js'); ?>"></script>
<script src="<?php cb('js/jscolor.js'); ?>"></script>
<script src="<?php cb('js/fine-uploader/fine-uploader.min.js'); ?>"></script>
<script src="<?php cb('js/WebAudioRecorder.min.js'); ?>"></script>
<script src="<?php cb('js/Recorder.js'); ?>"></script>
<!--<script src="<?php cb('js/jquery-sortable.js'); ?>"></script>-->
<!--<script src="<?php cb('js/jquery.svg.min.js'); ?>"></script>-->
<script src="<?php cb('js/Sortable.js'); ?>"></script>
<script src="<?php cb('js/templates.js'); ?>"></script>
<script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js"></script>
<script src="<?php cb('js/select2.full.min.js'); ?>"></script>
<script src="<?php cb('js/simplebar.js'); ?>"></script>
<script src="<?php cb('js/fonts.js'); ?>"></script>
<script src="<?php cb('js/main.js'); ?>"></script>
<script src="<?php cb('js/common.js'); ?>"></script>


    <script type="text/template" id="qq-template">
        <div class="qq-uploader-selector qq-uploader qq-gallery text-center" qq-drop-area-text="Drop files here">
            <div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
                <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
            </div>
            <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
                <span class="qq-upload-drop-area-text-selector"></span>
            </div>
            <div class="qq-upload-button-selector btn btn-primary ">
                <div>Upload a file</div>
            </div>
            <span class="qq-drop-processing-selector qq-drop-processing">
                <span>Processing dropped files...</span>
                <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
            </span>
            <ul class="qq-upload-list-selector qq-upload-list" role="region" aria-live="polite" aria-relevant="additions removals">
                <li>
                    <span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
                    <div class="qq-progress-bar-container-selector qq-progress-bar-container">
                        <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar"></div>
                    </div>
                    <span class="qq-upload-spinner-selector qq-upload-spinner"></span>
                    <div class="qq-thumbnail-wrapper">
                        <img class="qq-thumbnail-selector loadableImage" qq-max-size="120" qq-server-scale>
                        <button class="btn btn-success load-image" data-toggle="tooltip" title="Add Image"><i class="fa fa-plus"></i></button>
                    </div>
                    <button type="button" class="qq-upload-cancel-selector qq-upload-cancel">X</button>
                    <button type="button" class="qq-upload-retry-selector qq-upload-retry">
                        <span class="qq-btn qq-retry-icon" aria-label="Retry"></span>
                        Retry
                    </button>

                    <div class="qq-file-info">
                        <div class="qq-file-name">
                            <span class="qq-upload-file-selector qq-upload-file"></span>
                            <span class="qq-edit-filename-icon-selector qq-btn qq-edit-filename-icon" aria-label="Edit filename"></span>
                        </div>
                        <input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
                        <span class="qq-upload-size-selector qq-upload-size"></span>
                        <button type="button" class="qq-btn qq-upload-delete-selector qq-upload-delete">
                            <span class="qq-btn qq-delete-icon" aria-label="Delete"></span>
                        </button>
                        <button type="button" class="qq-btn qq-upload-pause-selector qq-upload-pause">
                            <span class="qq-btn qq-pause-icon" aria-label="Pause"></span>
                        </button>
                        <button type="button" class="qq-btn qq-upload-continue-selector qq-upload-continue">
                            <span class="qq-btn qq-continue-icon" aria-label="Continue"></span>
                        </button>
                    </div>
                </li>
            </ul>

            <dialog class="qq-alert-dialog-selector">
                <div class="qq-dialog-message-selector"></div>
                <div class="qq-dialog-buttons">
                    <button type="button" class="qq-cancel-button-selector">Close</button>
                </div>
            </dialog>

            <dialog class="qq-confirm-dialog-selector">
                <div class="qq-dialog-message-selector text-center font-weight-bold"></div>
                <div class="alert alert-warning">Be sure to remove it from each slides where you have used it!</div>
                <div class="qq-dialog-buttons">
                    <button type="button" class="qq-cancel-button-selector btn">No</button>
                    <button type="button" class="qq-ok-button-selector btn btn-danger">Yes</button>
                </div>
            </dialog>

            <dialog class="qq-prompt-dialog-selector">
                <div class="qq-dialog-message-selector"></div>
                <input type="text">
                <div class="qq-dialog-buttons">
                    <button type="button" class="qq-cancel-button-selector btn">Cancel</button>
                    <button type="button" class="qq-ok-button-selector btn">Ok</button>
                </div>
            </dialog>
            <button class="qq-collapse"><i class="fa fa-chevron-down"></i></button>
        </div>
    </script>

    <script type="text/template" id="qq-audioitem-template">
        <div class="qq-uploader-selector qq-uploader qq-gallery text-center" qq-drop-area-text="Drop files here">
            <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
                <span class="qq-upload-drop-area-text-selector"></span>
            </div>
            <div class="qq-upload-button-selector btn btn-primary ">
                <div>Upload a file</div>
            </div>


            <span class="qq-drop-processing-selector qq-drop-processing">
                <span>Processing dropped files...</span>
                <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
            </span>

            <div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container mt-2">
                <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
            </div>
            <div class="qq-progress-bar-container-selector">
                <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar"></div>
            </div>  

            <ul class="qq-upload-list-selector qq-upload-list" role="region" aria-live="polite" aria-relevant="additions removals">
                <li>
                    <span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
                    <div class="qq-progress-bar-container-selector qq-progress-bar-container">
                        <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar"></div>
                    </div>
                    <span class="qq-upload-spinner-selector qq-upload-spinner"></span>

                    <button type="button" class="qq-upload-cancel-selector qq-upload-cancel">X</button>
                    <button type="button" class="qq-upload-retry-selector qq-upload-retry">
                        <span class="qq-btn qq-retry-icon" aria-label="Retry"></span>
                        Retry
                    </button>

                    <div class="qq-file-info">
                        <div class="qq-file-name">
                            <span class="qq-upload-file-selector qq-upload-file"></span>
                            <span class="qq-edit-filename-icon-selector qq-btn qq-edit-filename-icon" aria-label="Edit filename"></span>
                        </div>
                        <input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
                        <span class="qq-upload-size-selector qq-upload-size"></span>
                        <button type="button" class="qq-btn qq-upload-delete-selector qq-upload-delete">
                            <span class="qq-btn qq-delete-icon" aria-label="Delete"></span>
                        </button>
                        <button type="button" class="qq-btn qq-upload-pause-selector qq-upload-pause">
                            <span class="qq-btn qq-pause-icon" aria-label="Pause"></span>
                        </button>
                        <button type="button" class="qq-btn qq-upload-continue-selector qq-upload-continue">
                            <span class="qq-btn qq-continue-icon" aria-label="Continue"></span>
                        </button>
                    </div>
                </li>
            </ul>

            <dialog class="qq-alert-dialog-selector">
                <div class="qq-dialog-message-selector"></div>
                <div class="qq-dialog-buttons">
                    <button type="button" class="qq-cancel-button-selector">Close</button>
                </div>
            </dialog>

            <dialog class="qq-confirm-dialog-selector">
                <div class="qq-dialog-message-selector text-center font-weight-bold"></div>
                <div class="alert alert-warning">Be sure to remove it from each slides where you have used it!</div>
                <div class="qq-dialog-buttons">
                    <button type="button" class="qq-cancel-button-selector btn">No</button>
                    <button type="button" class="qq-ok-button-selector btn btn-danger">Yes</button>
                </div>
            </dialog>

            <dialog class="qq-prompt-dialog-selector">
                <div class="qq-dialog-message-selector"></div>
                <input type="text">
                <div class="qq-dialog-buttons">
                    <button type="button" class="qq-cancel-button-selector btn">Cancel</button>
                    <button type="button" class="qq-ok-button-selector btn">Ok</button>
                </div>
            </dialog>
        </div>
    </script>


<script>
    var recorder = new CanvasRecorder('mainCanvasContainer', document, Konva, new Templates(), bootbox, jQuery);
    jQuery(function($){
        $.fn.select2.defaults.set( "theme", "bootstrap4" );

        recorder.init();
        recorder.userFolder = '<?php echo $user->getUserUrl();?>';
    });
    
    $('[data-toggle="tooltip"]').tooltip();
    var uploader = new qq.FineUploader({
        element: document.getElementById("uploader"),
        request: {
            endpoint: "/api/upload.php"
        },
        deleteFile: {
            forceConfirm: true,
            enabled: true,
            endpoint: "/api/upload.php"
        },
        chunking: {
            enabled: false,
            concurrent: {
                enabled: false
            },
            success: {
                endpoint: "/api/upload.php?done"
            }
        },
        resume: {
            enabled: true
        },
        retry: {
            enableAuto: true,
            showButton: true
        },
        validation:{
            allowedExtensions:['jpg','jpeg','png','gif','svg']
        },
        callbacks: {
            onComplete: function(id,name,data,xhr)
            {
                var fileid =this.getItemByFileId(id);
                var thumb_container = $(fileid).find('.qq-thumbnail-wrapper');
                $('img',thumb_container).hide();
                $(thumb_container).append('<img class="loadableImage trigger-collapse" src="'+data.url+'"/>')
                .attr('src',data.url);
            },
            onSubmit:function()
            {
                if($('a[href="#tab-background"]').is('.active'))
                {
                    this.setParams({isBack: true });
                }
                var $component = $('#imagesBrowserComponent');
                if($component.is('.collapsed'))
                {
                    $component.find('.qq-collapse').click();
                }
            }
        },
        thumbnails: {
            placeholders:{
                waitUntilResponse: true,
            },
            //customResizer: function(resizeInfo){
            //    return Promise.reject('Use url instead');
            //},
        }
    });
    $('.qq-collapse').on('click',function(){
        $(this)
            .find('i').toggleClass('fa-chevron-down fa-chevron-up').end()
            .closest('#imagesBrowserComponent').toggleClass('collapsed');
    });

    var audioItemUploader = new qq.FineUploader({
        template: 'qq-audioitem-template',
        element: document.getElementById("audioItemUploader"),
        request: {
            endpoint: "/api/upload.php"
        },
        deleteFile: {
            forceConfirm: true,
            enabled: true,
            endpoint: "/api/upload.php"
        },
        chunking: {
            enabled: false,
            concurrent: {
                enabled: false
            },
            success: {
                endpoint: "/api/upload.php?done"
            }
        },
        resume: {
            enabled: true
        },
        multiple: false,
        retry: {
            enableAuto: false,
            showButton: true
        },
        validation:{
            allowedExtensions:['mp3','wav'],
            itemLimit: 1,
        },
        callbacks: {
            onComplete: function(id,name,data,xhr)
            {
                if(data.success)
                {
                    recorder.attachAudioToItem(data.duration);
                }
                $('#modalAudioRecorder').modal('hide');
                
           },
            onSubmit:function(id,name)
            {
                this.setParams({
                    audioItemId: recorder._tmpCurrentAttatchToItemBtn.data('attach-id'),
                    projectId:recorder.projectId,
                    attachAudio: true}
                );
            }
        },
    });
    
    var audioLibraryUploader = new qq.FineUploader({
        template: 'qq-audioitem-template',
        element: document.getElementById("audioLibraryUploader"),
        request: {
            endpoint: "/api/upload.php"
        },
        deleteFile: {
            forceConfirm: true,
            enabled: true,
            endpoint: "/api/upload.php"
        },
        chunking: {
            enabled: false,
            concurrent: {
                enabled: false
            },
            success: {
                endpoint: "/api/upload.php?done"
            }
        },
        resume: {
            enabled: true
        },
        multiple: false,
        retry: {
            enableAuto: false,
            showButton: true
        },
        validation:{
            allowedExtensions:['mp3','wav'],
            itemLimit: 1,
        },
        callbacks: {
            onComplete: function(id,name,data,xhr)
            {
                if(data.success)
                {
                    $('#modalAddAudioToLibrary').modal('hide');
                    recorder.audioPlayerComponent.loadListFromServer();
                    //$('#btnAttachToKonvaItem').trigger('click');//show();
                }
                
           },
            onSubmit:function(id,name)
            {
                this.setParams({
                    addUserAudio: true}
                );
            }
        },
    });    
    
</script>
</body>
</html>
