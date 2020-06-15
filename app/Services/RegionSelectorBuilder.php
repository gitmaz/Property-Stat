<?php


namespace App\Services;

use Illuminate\Support\Facades\Cache;

trait RegionSelectorBuilder
{
    //Note: reusing this trait inside each of below function calls is not performance optimised but more readable and OOD complient
    // therefore we use caching to also optimise performance
    public static function getSelectorCached($commaSeparatedRegionName, $withAlias=false, $clearCache=false){

       if($clearCache){
           $value= self::getSelector($commaSeparatedRegionName, false);
           Cache::put('region_selector_phrase',$value, 1440);
           $withAliasValue= self::getSelector($commaSeparatedRegionName, true);
           Cache::put('region_selector_phrase_aliassed',$withAliasValue, 1440);
           if($withAlias){
               return $withAliasValue;
           }
           return $value;
       }

        $res= Cache::get('region_selector_phrase'.($withAlias?"_aliassed":""), function () use ($commaSeparatedRegionName, $withAlias)  {
            $value= self::getSelector($commaSeparatedRegionName, $withAlias);
            Cache::put('region_selector_phrase'.($withAlias?"_aliassed":""),$value, 1440);
            return $value;
        });

       return $res;
    }

    public static function getSelector($commaSeparatedRegionName, $withAlias=false){
        $regionSelectorPhrase=self::getRegionSelector($commaSeparatedRegionName);

        //use below case for calculating percentages which needs joins and aliasses
        if(!$withAlias) {
            $regionSelectorPhrase = (str_replace("p.", "", $regionSelectorPhrase));
        }

        return $regionSelectorPhrase;
    }



    private static function getRegionSelector($commaSeparatedRegionName){
        $regionNameParts=explode(",", $commaSeparatedRegionName);
        $regionPartsCount=count($regionNameParts);
        if($regionPartsCount==0 || $regionPartsCount>3 ){
            throw new \Exception("wrong region name (correct format is: country[,state[,suburb]]} ). $commaSeparatedRegionName is inputted!");
        }else{
            $regionSelectorPhrase="";
            foreach($regionNameParts as $ind=>$regionNamePart){
                $regionNamePart=trim($regionNamePart);
                switch($ind){
                    case "0":
                        $regionSelectorPhrase.="p.country='$regionNamePart'";
                        break;
                    case "1":
                        $regionSelectorPhrase.=" and p.state='$regionNamePart'";
                        break;
                    case "2":
                        $regionSelectorPhrase.=" and p.suburb='$regionNamePart'";
                        break;
                }
            }
        }
        return $regionSelectorPhrase;
    }
}
