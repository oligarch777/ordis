<?php
namespace Renovate\MainBundle\Services;

use Doctrine\ORM\Mapping as ORM;

class Transliterater
{
	private $replace = array(
						    "А"=>"A","а"=>"a",
						    "Б"=>"B","б"=>"b",
						    "В"=>"V","в"=>"v",	
						    "Г"=>"G","г"=>"g",
						    "Д"=>"D","д"=>"d",
						    "Е"=>"Ye","е"=>"e",
							"Є"=>"Ye","є"=>"e",
						    "Ё"=>"Ye","ё"=>"e",
						    "Ж"=>"Zh","ж"=>"zh",
						    "З"=>"Z","з"=>"z",
						    "И"=>"I","и"=>"i",
							"І"=>"I","і"=>"i",
							"Ї"=>"JI","ї"=>"ji",
						    "Й"=>"Y","й"=>"y",
						    "К"=>"K","к"=>"k",
						    "Л"=>"L","л"=>"l",
						    "М"=>"M","м"=>"m",
						    "Н"=>"N","н"=>"n",
						    "О"=>"O","о"=>"o",
						    "П"=>"P","п"=>"p",
						    "Р"=>"R","р"=>"r",
						    "С"=>"S","с"=>"s",
						    "Т"=>"T","т"=>"t",
						    "У"=>"U","у"=>"u",
						    "Ф"=>"F","ф"=>"f",
						    "Х"=>"Kh","х"=>"kh",
						    "Ц"=>"Ts","ц"=>"ts",
						    "Ч"=>"Ch","ч"=>"ch",
						    "Ш"=>"Sh","ш"=>"sh",
						    "Щ"=>"Shch","щ"=>"shch",
						    "Ъ"=>"","ъ"=>"",
						    "Ы"=>"Y","ы"=>"y",
						    "Ь"=>"J","ь"=>"j",
						    "Э"=>"E","э"=>"e",
						    "Ю"=>"Yu","ю"=>"yu",
						    "Я"=>"Ya","я"=>"ya",
							"-"=>"_","!"=>"_",
							"?"=>"_","@"=>"_",
							"#"=>"_","№"=>"_",
							"$"=>"_",";"=>"_",
							"%"=>"_","^"=>"_",
							":"=>"_","&"=>"_",
							"*"=>"_","("=>"_",
							")"=>"_","+"=>"_",
							"="=>"_","/"=>"_",
							"\\"=>"_","|"=>"_",
							"["=>"_","]"=>"_",
							"{"=>"_","}"=>"_",
							"'"=>"j",'"'=>"_",
							"~"=>"_","’"=>"j",
							"`"=>"j","<"=>"_",
							">"=>"_","."=>"_",
							","=>"_"," "=>"_"
						);
	
    public function transliterate($string)
    {
		return strtr($string, $this->replace);
    }
}