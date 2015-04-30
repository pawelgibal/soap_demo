<html>

<head>
  <title></title>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" >
</head>
<body>
<?php
// link do katalogu z grafika aukcji
 $path = "/home/pawel/as_2013/altershop_pl_2014/koszulki/wyniki_bluzy/muzyczne/17_01_2015/";
 


define('OPTION_THUMB',2);
  //  const OPTION_THUMB = 2;
    class WebAPISoapClient extends SoapClient
    {
    // jedynka to kod kraju dla Polski, o tym dalej
    const COUNTRY_PL = 1;
    const QUERY_ALLEGROWEBAPI = 1; 

	const LIFETIME_30DAYS = 5;
        const TRANSPORT_COST_BUYER = 1;
        const TRANSPORT_OPTION_POSTPRIORITY = 2;
        const TRANSPORT_OPTION_PERSONAL = 8;
        const PAYMENT_OPTION_PREPAID = 1;
	const PAYMENT_OPTION_ALLEGRO = 4;


   public function __construct()
   {
   parent::__construct('https://webapi.allegro.pl/uploader.php?wsdl');
   }
   }
// klucz web api
    $allegroVerKey ='yyyyyyyy';

    define('ALLEGRO_LOGIN', 'yyyyy');
    define('ALLEGRO_PASSWORD', 'y');
    define('ALLEGRO_KEY', 'yyyy');
    define('ALLEGRO_COUNTRY', 1);


  
  // łączymy się z Allegro WebAPI
  $client = new WebAPISoapClient();
   
	
  // w ten sposób zadbamy, aby ewentualny błąd nie narobił szkód
  try
  {
    try
    {
    // próba logowania
    $session = $client->doLoginEnc(ALLEGRO_LOGIN, base64_encode( hash('sha256', ALLEGRO_PASSWORD, true) ), ALLEGRO_COUNTRY, ALLEGRO_KEY, $allegroVerKey);
    }
  catch(SoapFault $error)
  {
  // błąd niepoprawnego klucza wersji coś w serwisie się zmieniło
    if($error->faultcode == 'ERR_INVALID_VERSION_CAT_SELL_FIELDS')
    {
    // pobieramy aktualny klucz wersji
     $version = $client->doQuerySysStatus(WebAPISoapClient::QUERY_ALLEGROWEBAPI, ALLEGRO_COUNTRY, ALLEGRO_KEY);
     $allegroVerKey = $version['ver-key'];
   
      // ponowna próba logowania, już z nowym kluczem
        $session = $client->doLoginEnc(ALLEGRO_LOGIN, base64_encode( hash('sha256', ALLEGRO_PASSWORD, true) ), ALLEGRO_COUNTRY, ALLEGRO_KEY, $allegroVerKey);
     }
  // każdy inny błąd to już poważny problem
     else
        {
        throw $error;
        }
  }
   
  // udało nam się zalogować
  echo 'Logowanie poprawne. Uzyskany klucz sesji to: ', $session['session-handle-part'].'<br><br>';
  }
  catch(SoapFault $error)
  {
  echo 'Błąd ', $error->faultcode, ': ', $error->faultstring, "n";
  }

//      pusta struktura tablicy
 $empty = new stdClass();
    $empty->{'fvalue-string'} = '';
    $empty->{'fvalue-int'} = 0;
    $empty->{'fvalue-float'} = 0;
    // to pole w formie pustej ma zawierać spację
    $empty->{'fvalue-image'} = ' ';
    $empty->{'fvalue-datetime'} = 0;
    $empty->{'fvalue-date'} = '';
$empty->{'fvalue-range-int'} = array ('fvalue-range-int-min' => 0,
                    'fvalue-range-int-max' => 0);
$empty->{'fvalue-range-float'} = array(
                    'fvalue-range-float-min' => 0,
                    'fvalue-range-float-max' => 0);
$empty->{'fvalue-range-date'} = array(
                    'fvalue-range-date-min' => 0,
                    'fvalue-range-date-max' => 0);

    $form = array();
    $tablica=array();
    $dirss=Array();
    
    if ($dir = @opendir($path)) {     // Jezeli otworzy sciezke
  //  TUTAJ POCZATEK FORMOWANA AUKCJI
    while(false !== ($dirs = readdir($dir))) {  // dla kazdego KATALOGU formuj aukcje  ! NIE dla . i .. ! ponizej WARUNEK !
	$i=$i+1;
	$dirss[$i] = $dirs;
        echo ("KATALOG dirrs:".$dirss[$i]."<br>");
	
	if ($dirss[$i] == '.' ) 		 
			{
			$dirss[$i]=str_replace(".","",$dirss[$i]); }
			if ( $dirss[$i] == '..' ) 		 
			{
			$dirss[$i]=str_replace("..","",$dirss[$i]); }

	if ( ($dirss[$i] !== '') and ($dirss[$i] !== 'zrodlo')) 		 
	{ 

        $d = dir($path.$dirss[$i]); 
       
	$path=strtolower (strtoupper( ($path)));
	
	//$kat=rtrim($d->path,$path);
	$kat=str_replace($path,"",$d->path);  
	 $kat=strtolower (strtoupper( ($kat)));
	$kat=str_replace("_"," ",$kat);
	$kat_search=str_replace(" ","+",$kat);	
	//echo("katalog:".$kat."<br>");
	$kat_upp=strtoupper( $kat);  // zmiennna z 1 czescia tytulu aukcji KAPITALIKI

	// WCZYTAL PILIKI DO TABLICY
         while($entry=$d->read()) { 
			
			//echo "entry:".$entry."<br><br>"; 
			if ( $entry == '.' ) 		 
			{
			$entry=str_replace(".","",$entry); }
			if ( $entry == '..' ) 		 
			{
			$entry=str_replace("..","",$entry); }
		
			if ( ($entry !== '') and (!is_dir($path.$entry)) and ($entry !== 'zrodlo') and ($entry !== 'Thumbs.db')) 		 
			{ 
			//echo ("");
	
			$tablica[$l] = $entry; 
			$l=$l+1;
			//echo($path.$entry."<br>-przeszedlem przez warunki na .. i na nie bycie katalogiem<br>");
			echo ($entry." ".$l.", <br>");
			}
		}
        
	 
		if ($tablica !== NULL) { 	       // domyslne sortowanie tablicy
		sort($tablica);
		 }
		$l=0;	
	// TYTUL AUKCJI
    // pamiętaj, że maksymalna długość 50 "znaków" liczona jest w bajtach, dlatego polskie znaki, czy encje HTMLa liczone są za kilka bajtów FIELDS OF THE NEPHILIM
	if (strlen($kat_upp)<=45 ) {  //FIELDS OF THE NEPHILIM koszulka
		$tytul_suffix=' bluza';
	}
	 if (strlen($kat_upp)<=30 ) {  //FIELDS OF THE NEPHILIM koszulka
		$tytul_suffix=' bluza super jakość';
	} 
	if (strlen($kat_upp)<=20 ) {
		$tytul_suffix=' Piekielnie dobra bluza';
		}
	 if (strlen($kat_upp)>41 ) {
		$tytul_suffix=' ';
		}	

     // $tytul_suffix=' t-shirt';  Piekielnie dobra koszulka (29+8) =37 - max 12 znakow na nazwe
    $field = clone $empty;
    $field->{'fid'} = 1;
    $field->{'fvalue-string'} =$kat_upp.$tytul_suffix;   // pierwsza czesc t onazwa katalogu
    $form[] = $field;
    echo('<br>'.$kat_upp.$tytul_suffix.' DL:'.strlen($kat_upp.$tytul_suffix).'<br>');

    //KATEGORIA ALLEGRO ID
    $field = clone $empty;
    $field->{'fid'} = 2;
    $field->{'fvalue-int'} =20766 ;         // 87843- odziez meska bluzy //100135-filmowe, 87913-tshirty , 87876odziez meska dlugi rekaw      //20766 - gadzety muzyczne    // t-shirt meski (pozostale) 91194
    $form[] = $field;

  // KATEGORIA w SKLEPIE !
    $field = clone $empty;
    $field->{'fid'} = 31;
    $field->{'fvalue-int'} = 475465;     // bluzy (bez kaptura) 475465 // 377862 dlugi rekaw   bluzy z kapturem 378668    // krotki rekaw 443005
    $form[] = $field;

     // CZAS WYSTAWIENIA
    $field = clone $empty;
    $field->{'fid'} = 3;
    $field->{'fvalue-datetime'} = time();
    $form[] = $field;

    // CZAS TRWANIA        sell-form-desc] => 3|5|7|10|14|30
     // [sell-form-opts-values] => 0|1|2|3|4|5

    $field = clone $empty;
    $field->{'fid'} = 4;
    $field->{'fvalue-int'} = WebAPISOAPClient::LIFETIME_30DAYS;
    $form[] = $field;

    // LICZBA SZTUK
    $field = clone $empty;
    $field->{'fid'} = 5;
    $field->{'fvalue-int'} = 2;
    $form[] = $field;

    // CENA WYWOLAWCZA
    $field = clone $empty;
    $field->{'fid'} = 8;
    $field->{'fvalue-float'} = 64.90; // CENA Promocyjna WIOSNA
    $form[] = $field;

    $field = clone $empty;
    $field->{'fid'} = 9;
    $field->{'fvalue-int'} = WebAPISOAPClient::COUNTRY_PL;
    $form[] = $field;

    // 16 to województwo zachodniopomorskie, numer województwa można pobrać z listy opisu pola
    $field = clone $empty;
    $field->{'fid'} = 10;
    $field->{'fvalue-int'} = 1;
    $form[] = $field;

    $field = clone $empty;
    $field->{'fid'} = 11;
    $field->{'fvalue-string'} = 'Wrocław';
    $form[] = $field;

     // koszty transportu sprzedaca / kupujacy
    $field = clone $empty;
    $field->{'fid'} = 12;
    $field->{'fvalue-int'} = WebAPISOAPClient::TRANSPORT_COST_BUYER;
    $form[] = $field;

	// opcje transportu	
    // flagi składamy przez ich logiczne sumowanie
    $field = clone $empty;
    $field->{'fid'} = 13;
    $field->{'fvalue-int'} = WebAPISOAPClient::TRANSPORT_OPTION_PERSONAL | WebAPISOAPClient::TRANSPORT_OPTION_POSTPRIORITY;
    $form[] = $field;
     // formy platnosci // Formy pĹatnoĹci0ZwykĹy przelew|-|-|-|Inne rodzaje pĹatnoĹci|Wystawiam faktury VAT|-1|2|4|8|16|32|64, 2
    $field = clone $empty;
    $field->{'fid'} = 14;
    $field->{'fvalue-int'} = 33; // 32+1 ! platnosc przelewem i wystawiam VAT
    //$field->{'fvalue-int'} = WebAPISOAPClient::PAYMENT_OPTION_PREPAID | WebAPISOAPClient::PAYMENT_OPTION_ALLEGRO;
    $form[] = $field;
    // wyroznienie aukcji
    $field = clone $empty;
    $field->{'fid'} = 15;
    $field->{'fvalue-int'} = OPTION_THUMB;
    $form[] = $field;

  //  $i = 0;

    // maksymalnie 8 zdjęć!
   /* foreach( array('/home/pawel/as_2010/sklepgothic_pl/grafika-produkty/wyniki_allegro_ebm_ls/jpg/ebm_01_ls.jpg') as $image)
    */
	// PIERWSZA GRAFIKA  - miniaturka - specjalnie oznakowana z osobnego katalogu - lub zwykla pierwsza z katalogu
	//$pierwszy_obrazek=$path.$tablica[0];
	$pierwszy_obrazek=('/home/pawel/as_2013/altershop_pl_2014/koszulki/wyniki_podpisane_bluzy_miniatury_allegro/'.$tablica[0]);
	//$pierwszy_obrazek=$path.$dirss[$i]."/".$tablica[0];
	
        $field = clone $empty;
        $field->{'fid'} = 16 ;  //+ $i;
       
       if (	$field->{'fvalue-image'} =file_get_contents($pierwszy_obrazek) == FALSE )
		// $field->{'fvalue-image'} =file_get_contents($pierwszy_obrazek) ;
   	{
    	echo("uzywam pierwszego z katalogu:");   
                                echo($pierwszy_obrazek." -  obrazek NIE podpisany z katalogu <br><br>");
				echo($path.$dirss[$i]."/".$tablica[0]."<br><br>");  
				$pierwszy_obrazek=$path.$dirss[$i]."/".$tablica[0];
					
				$field->{'fvalue-image'} = file_get_contents($pierwszy_obrazek);	 
    	
    	}
    	 else {
				echo ("pobralem obrazek z podpisem z boku<br>");
				$field->{'fvalue-image'} =file_get_contents($pierwszy_obrazek);
    	 	}
         
        $form[] = $field;


       // OPIS !
	$opis='';
        	$opis.='
....
<p>Zapraszamy na aukcję, kt&oacute;ra na pewno wzbudzi Twoje zainteresowanie</p>
<p>bluza z motywem <span class="nazwak">        	
 ';
	$opis.=$kat_upp;
	$opis.='<br /></span></p>
<p>Wybierz jeden z poniższych wzor&oacute;w</p>
<p class="redd">Symbol i rozmiar podaj w formularzu zakupu np. BLU_01 rozm XL</p>
</div>
<div class="galeria">
<table style="width: 100%;">
<tbody>
';
    // generowanie tabeli ale BEZ table sa to wewnetrzne wiersze
            $l_pow=sizeof($tablica) ;
	        $iC=1;
	        if ( $l_pow==1) 
	        	{
	        		 $cat_per_row=1;
	        	} else
	        		 {
	        			$cat_per_row=2;
	        		}
		
		$cell_width=intval(100/$cat_per_row);		

		 for($k = 0; $k < sizeof($tablica); ++$k) {
			if ($iC == 1) {
					$related_product_html.= "<tr>";
				}
				
	        	if ( $l_pow==1) { 
                                    $related_product_html .= '<td><img style="width: auto; display:block; margin-left: auto; margin-right: auto;" ';
	        		}   
                                    else {
  	 	 			  $related_product_html .= '<td><br><img '; 
  	 	 			  }
					
					$related_product_html .='src="http://adres na zewnetrznym serwerze /'.$tablica[$k].'"></td>';
					
			if ($iC == $cat_per_row) {
					$related_product_html.= "</tr>";
					$iC = 1;
				}
				else {
					$iC++;
				}
			}   // koniec do wHILE
                    $mod= $l_pow % $cat_per_row;		 
                    if ($mod <> 0) 
			{
				$related_product_html.= "<td></td></tr>";		
			}
                     
                    $k=0;
                    unset($tablica); 
                	//    koniec generowania GALERI
	$opis.=$related_product_html;  // dodanie galerii
	$related_product_html="";  // zerowanie galerii
	//dalsza czesc opisu rozmiarowki tabela romiarow 
	$opis.='</tbody>
</table>
</div>
.....
';

  //  echo($opis);
	
    $field = clone $empty;
    $field->{'fid'} = 24;
    $field->{'fvalue-string'} = $opis;
    $form[] = $field;

	// sztuki/ komplety
    $field = clone $empty;
    $field->{'fid'} = 28;
    $field->{'fvalue-int'} = 0;
    $form[] = $field;

    // czy kup TERAZ lub licytacja 0 ALBO sklep 1
    $field = clone $empty;
    $field->{'fid'} = 29;
    $field->{'fvalue-int'} = 1;
    $form[] = $field;

    // wznawiania AUTOMATYCZNE w sklepie       0 bez wznawiania , Nie wznawiaj|Wznów z pełnym zestawem przedmiotów|Wznów tylko z przedmiotami niesprzedanymi
    $field = clone $empty;
    $field->{'fid'} = 30;
    $field->{'fvalue-int'} = 0; // wznawiaj z pelnym zestawem
    $form[] = $field;


    // KOD POCZTOWY
    $field = clone $empty;
    $field->{'fid'} = 32;
    $field->{'fvalue-string'} = '50-520';
    $form[] = $field;
    
    // 1 konto bankowe
    $field = clone $empty;
    $field->{'fid'} = 33;
    $field->{'fvalue-string'} = '64 1140 2004 0000 3402 4388 3690';
    $form[] = $field;
    
    // 2 konto bankowe
   // $field = clone $empty;
   // $field->{'fid'} = 34;
  //  $field->{'fvalue-string'} = '64 1140 2004 0000 3402 4388 3690';
   // $form[] = $field;
    
    // ODBIOR OSOBISTY  pierwsa sztuka
    // fid:35Darmowe opcje przesyĹki0OdbiĂłr osobisty|PrzesyĹka elektroniczna (e-mail)|Odbir osobisty po przedpĹacie1|2|4, 2 int
    $field = clone $empty;
    $field->{'fid'} = 35;
  //  $field->{'fvalue-float'} = 0.0; // 
    $field->{'fvalue-int'} = 5; // 1+4
    $form[] = $field;
    
 

 // KOSZT WYSYLKI POBRANIE PRIO  pierwsa sztuka
    $field = clone $empty;
    $field->{'fid'} = 42;
    $field->{'fvalue-float'} = 17.00;
    $form[] = $field;
    
// KOSZT WYSYLKI POBRANIE PRIO   KOLEJNA sztuka
    $field = clone $empty;
    $field->{'fid'} = 142;
    $field->{'fvalue-float'} = 1.0;
    $form[] = $field;    
    
// KOSZT WYSYLKI ilosc w POBRANIE PRIO  
    $field = clone $empty;
    $field->{'fid'} = 242;
    $field->{'fvalue-int'} = 8;
    $form[] = $field;
    
     // KOSZT WYSYLKI PACZKA EKO pierwsa sztuka
   // $field = clone $empty;
   // $field->{'fid'} = 36;
   // $field->{'fvalue-float'} = 12;
   // $form[] = $field;

    // KOSZT WYSYLKI PACZKA EKO  KOLEJNA sztuka
  //  $field = clone $empty;
   // $field->{'fid'} = 136;
  //  $field->{'fvalue-float'} = 1.0;
   // $form[] = $field;

    // KOSZT WYSYLKI ilosc w PACZCE  EKO
   // $field = clone $empty;
   // $field->{'fid'} = 236;
   // $field->{'fvalue-int'} = 8;
   // $form[] = $field;
        
    // KOSZT WYSYLKI PACZKA PRIO  pierwsa sztuka
    $field = clone $empty;
    $field->{'fid'} = 38;
    $field->{'fvalue-float'} = 13.50;
    $form[] = $field;

    // KOSZT WYSYLKI PACZKA PRIO  KOLEJNA sztuka
    $field = clone $empty;
    $field->{'fid'} = 138;
    $field->{'fvalue-float'} = 0.0;
    $form[] = $field;

    // KOSZT WYSYLKI ilosc w PACZCE
    $field = clone $empty;
    $field->{'fid'} = 238;
    $field->{'fvalue-int'} = 8;
    $form[] = $field;

    // KOSZT WYSYLKI LIST POL PRIO  pierwsa sztuka
    $field = clone $empty;
    $field->{'fid'} = 43;
    $field->{'fvalue-float'} = 13.50;
    $form[] = $field;

    // KOSZT WYSYLKI LIST POL PRIO  KOLEJNA sztuka
    $field = clone $empty;
    $field->{'fid'} = 143;
    $field->{'fvalue-float'} = 0;
    $form[] = $field;

    // KOSZT WYSYLKI LIST  ilosc W LISCIE
    $field = clone $empty;
    $field->{'fid'} = 243;
    $field->{'fvalue-int'} = 1;
    $form[] = $field;
    
    // KOSZT WYSYLKI LIST POL EKO pierwsa sztuka
    $field = clone $empty;
    $field->{'fid'} = 41;
    $field->{'fvalue-float'} = 10.00;
    $form[] = $field;

    // KOSZT WYSYLKI LIST POL EKO KOLEJNA sztuka
    $field = clone $empty;
    $field->{'fid'} = 141;
    $field->{'fvalue-float'} = 0;
    $form[] = $field;

    // KOSZT WYSYLKI LIST  ilosc W LISCIE EKO
    $field = clone $empty;
    $field->{'fid'} = 241;
    $field->{'fvalue-int'} = 1;
    $form[] = $field;

    // WYSYLKA W CIAGU ( pole wymagane aby aucka byla ze STANDARDEM ALLEGRO  fid   340 - "Wysyłka w ciągu" (do wyboru wartości 24/48/72/96/120/168/240/336/504 godzin).
    $field = clone $empty;
    $field->{'fid'} = 340;
    $field->{'fvalue-int'} = 72; //  96 - 4dni , 7 dni = 24*7 = 168
    $form[] = $field;

    // POLA SPECIALNE dla kategorii ODZIEZ MESKA odziez-i-bielizna-meska-1455
    //  [sell-form-desc] =>  -- Wybierz -- |Nowe|Używane
    // [sell-form-opts-values] => 0|1|2

    $field = clone $empty;
    $field->{'fid'} = 21152;
    $field->{'fvalue-int'} = 1;   // NOWE
    $form[] = $field;


   /*  $field = clone $empty;
    $field->{'fid'} = 1846;
    $field->{'fvalue-int'} = 2;   // KOLOR
    $form[] = $field;  */
    
    $local = uniqid();

	// SPRAWDZENIE PRZED WYSTAWIENIEM
	// STANDARD ALLEGRO 
       //   $standard=$client->doCheckNewAuctionExt($session['session-handle-part'], $form);
	// echo("standard allegro".$standard['item-is-allegro-standard']."<br>");
      // WYSTAWIENIE AUKCJI !!!!
         $item = $client->doNewAuctionExt($session['session-handle-part'], $form, 0, $local);
        // $check = $client->doVerifyItem($session['session-handle-part'], $local);

      //  echo("check".$check['item-id']."<br>");
        echo("item".$item['item-id']."<br>");
	//echo("item-id".$item['item-id']."<br>");

  //  if($item['item-id'] == $check['item-id'])
  //  {
        echo '<p>Wystawiono przedmiot <a href="http://allegro.pl/item' . $item['item-id'] . '.html">' . $item['item-id'] . '</a>.</p>';
 //   }
//     else
//     {
//         echo '<p class="error"> sprawdz > <a href="http://allegro.pl/item' . $item['item-id'] . '.html">'. $item['item-id'].'</a></p>';
//     }
//        $item['item-id']=0;
// 	$check['item-id']=0;
       $local=0;
// KONIEC POJEDYNCZEJ AUKJCI AUKCJI
     }  // koniec DLA zew spr IF ( dla katalogow0 nie chcemy . .. 
 } // koniec DLA KAZDEGO KATALOGU
} // koniec jezeli otworzy sciezke
echo ('<br><br>KONIEC ?');

?>
</body>
</html>