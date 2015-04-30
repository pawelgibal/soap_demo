<?php
$wieksza_tab=array();

$plik_id=fopen("/home/pawel/as_2013/id_torby_do_zmiany_09_04ok_cz4.csv", "r");
  /*
   * przykladowa zawartosc pliku csv - usyzkana z osobnego programu - wyszukiwarki produktow
   * 5175969173;Torba ONE DIRECTION różne wzory
   * 5179290545;Torba DEPECHE MODE różne wzory
   * 5179290558;Torba EPICA różne wzory
   */
 $form = array();
    


 /* z dokumentacji Allegro
 session-handle | string | wymagany Identyfikator sesji użytkownika, uzyskany za pomocą metody doLogin(Enc).
item-id | long | wymagany  Identyfikator oferty.
new-starting-price | float | niewymagany (wymagany gdy podano new-reserve-price lub nie podano new-buy-now-price)
Nowa wartość dla ceny wywoławczej.
new-reserve-price | float | niewymagany
Nowa wartość dla ceny minimalnej.
new-buy-now-price | float | niewymagany (wymagany gdy nie podano new-starting-price)
Nowa wartość dla ceny Kup Teraz!

Orientacyjna struktura wywołania metody
$dochangepriceitem_request = array(
   'session-handle' => '22eb99326c6be29aa16d07d622bcfbcbee94ad54846f2f4e03_1',
   'item-id' => 1027314005,
   'new-starting-price' => 35.00,
   'new-reserve-price' => 0.00,
   'new-buy-now-price' => 0.00
);  */

try{

  define('COUNTRY_CODE', 1);
    define('WEBAPI_USER_LOGIN', 'xxxxx');
    define('WEBAPI_USER_ENCODED_PASSWORD', base64_encode(hash('sha256', 'xxxxxx', true)));
    define('WEBAPI_KEY', 'xxxxxxxxx');
     
    $options['features'] = SOAP_SINGLE_ELEMENT_ARRAYS;
    
    $soapClient = new SoapClient('https://webapi.allegro.pl/service.php?wsdl', $options);
    $request = array(
    'countryId' => COUNTRY_CODE,
    'webapiKey' => WEBAPI_KEY
    );
 
    $component=4; // (wartosci od 1 do 5 ? WebAPI(0) 	Program(1) 	Kategorie(2) 	Atrybuty(3) 	Formularz(4)  Lista serwisów(5)- 
   // najczesciej zmieniane kategorie i formularz , klucz wersji jest dla wszystkich taki sam, wiec )
    $tablica_wejsciowa_doQuerySysStatus= array(
	'sysvar'=>$component,
	'countryId'=>COUNTRY_CODE,
	'webapiKey'=>WEBAPI_KEY
	); 
    
    $wynikSysStatus = $soapClient ->doQuerySysStatus($tablica_wejsciowa_doQuerySysStatus);
   // $version = $soapClient ->doQuerySysStatus(array($component, COUNTRY_CODE, WEBAPI_KEY));
   $allegroInfo = $wynikSysStatus->info; 
   $allegroVerKey = $wynikSysStatus->verKey; 
  
   // echo ("info:".$allegroInfo."<br>");
   // echo ("wersja allegro API wg nazwy:".$allegroVerKey."<br>"); 
  
    
    try { 
    $request_doLogin = array(
    'userLogin' => WEBAPI_USER_LOGIN,
    'userHashPassword' => WEBAPI_USER_ENCODED_PASSWORD,
    'countryCode' => COUNTRY_CODE,
    'webapiKey' => WEBAPI_KEY,
    'localVersion' => $allegroVerKey,
    );
  
    $session = $soapClient->doLoginEnc($request_doLogin);
     echo 'Logowanie poprawne. Uzyskany klucz sesji to: ', $session->sessionHandlePart.'<br><br>'; 
     } catch(Exception $e) {
  	  echo $e;
     } // koniec to TRY proba logowania
     
   

// Wczytanie listy z pliku do tablicy
 $ll=0;
    if ($plik_id) {
   		 while (!feof($plik_id)) {
     		   $buffer = fgets($plik_id);
                            if ($buffer <> '') {
                           // echo $buffer;
                            list($id, $nazwa_pliku) = explode(";", $buffer);
                             $tab_tytulow[]=array("id"=>$id, "plik"=>$nazwa_pliku);
							
                                		}
					}
	
                    }
			echo("tablica id / tytul<br>");
			print_r($tab_tytulow);	

  //  glowna petla
     
     foreach ($tab_tytulow as $licznik => $tt) 	{ 
        
         
        // $field->{'fvalueImage'} = file_get_contents($pierwszy_obrazek);	
       	   		 
   	/*	   // pierwszy obrazek = miniaturka aukcji
  				$tab_item_1=array('fid'=>16,'fvalueImage'=> $pierwszy_obrazek);
   		   // teraz kilka takich tablic dla roznych fidow
   		   $wieksza_tab[]=$tab_item_1;
   		   
   		   // wznowienie aukcji
   		   $tab_item_2=array('fid'=>30,'fvalueInt'=>1);
   		   $wieksza_tab[]=$tab_item_2;
   		   
   		   //zmiana ceny
   		  $tab_item_3=array('fid'=>8,'fvalueFloat'=>29.00);
   		   $wieksza_tab[]=$tab_item_3;
   	*/	   
   		   // POBRANIE WARTOSCI DLA TYTULU AUKCJI FID 0 ! - a do dodania FID 1 
   	 $struktura_pola_oferty =array(
      	 'sessionId' => $session->sessionHandlePart,
      	 'itemId'=>(float)$tt['id']
      	 );
      	 echo ('ID:'.$tt['id']);
      	 
      	 try {
      		 $wynik_pobierz_pola= $soapClient->doGetItemFields($struktura_pola_oferty);
             }
              catch(Exception $e) {
      	 	echo('blad w petli akcji dla ID:'.$tt['id'].'<br>');
                  echo $e.'<br>';
              }
   		// pobierz tytul aukcji		 } 
            $opis=$wynik_pobierz_pola->itemFields->item[0]->fvalueString;
   		        		   
                    // print_r ( $wynik_pobierz_pola->itemFields);
   	  		// echo(	' _<textarea>'.$opis.'</textarea>');
   		     	   		
   		    $znajdz='Torba';
   	   $pos = strpos($opis, $znajdz);

   	    if ($pos ===false) {
   	    	echo(' NIE znalazlem <br>');
   	    	}
   	    	else {
                    echo(' znalazlem:'. $znajdz.' na pozycji:'.$pos.'<br>');
                    //ZAMIEN NA :
                    $opis_zmieniony=str_replace ($znajdz,'Torba1',$opis);
                      }
                      
            $tab_item_4=array('fid'=>1,'fvalueString'=>$opis_zmieniony);
            $wieksza_tab[]=$tab_item_4;	   
   		   // JEZELI WYSKAKUJE BLAD - blednie przekazane pole np fid: 1846 to znaczy ze dla jakiegos FIDA zostala wskazana 
   		   // BLEDNA WARTOSC np nie istniejaca kategoria

   	   $dochangeitemfields_request = array(
   'sessionId' =>  $session->sessionHandlePart,
   'itemId' =>(float) $tt['id'],
   'fieldsToModify' => $wieksza_tab, 
   'previewOnly' => 0
    );


//echo('<br><br>');
   		// ('<br><br>$dochangeitemfields_request:'.$dochangeitemfields_request.'<br><br>');     		      		     		       		 

   	    try {
     		  $wynik_aukcja_zmieniona= $soapClient->doChangeItemFields($dochangeitemfields_request);
     		  }
     		  catch(Exception $e) {
                  echo('blad w petli akcji dla ID:'.$tt['id'].'<br>');
  				  echo $e.'<br>';
   		 } 
   		
   	 
   echo '<p>Zmienilem <a href="http://allegro.pl/item' . $tt['id'] . '.html">' . $tt['id'].' '.$tt['plik'].'</a>.</p>';
 //print_r($wynik_aukcja_zmieniona); // wyswietla cala tablice  	
   echo('<br>');
   
  } // koniec glownej petli
  /*
  item-info | string
Informacja o wysokości ew. dopłaty (lub zwrotu) do oferty.
item-id | long Identyfikator oferty.

Orientacyjna struktura odpowiedzi serwera
$dochangepriceitem_response = array(
   'item-info' => '-0,50 zł',
   'item-id' => 1027314005
);
  */
  // koniec to try	 
 } catch(Exception $e) {
     echo $e;
    }
?>