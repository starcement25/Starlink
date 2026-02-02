<?php
namespace App\Utils;

use App\Models\Page;

class Helper{


    public static function getAllPages()
    {
        $pages = Page::all();
        return $pages ;
    }

    public static function getElementType($type)
    {
        $element = [
                    'select' => 'Select',
                    'searchable_select' => 'Searchable Select',
                    'text' => 'Text Box', 'label'=> 'Label',
                    'checkbox'=> 'Check Box',
                    'heading'=> 'Heading',
                    'rank'=> 'Ranking'
                     ] ;
        
        return $element[$type] ?? "" ;
    }
}