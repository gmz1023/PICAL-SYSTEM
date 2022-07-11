<?php
spl_autoload_register(function ($name) {
	$file = $name.'.php';

		$path = "./includes/class/".$file;
try {
   	if (file_exists($path)) {
       require_once($path);
	  
   	} else {
       die("The file {$path} could not be found! \n");

   	}
}catch(exception $e) { echo $e->getMessage();}
});
function get_pagination_links($current_page, $total_pages, $url)
{
    $links = "";
    if ($total_pages >= 1 && $current_page <= $total_pages) {
        $links .= "<a href=\"{$url}&page=1\">1</a>";
        $i = max(2, $current_page - 5);
        if ($i > 2)
            $links .= " ... ";
        for (; $i < min($current_page + 6, $total_pages); $i++) {
            $links .= "<a href=\"{$url}&page={$i}\">{$i}</a>|";
        }
        if ($i != $total_pages)
            $links .= " ... ";
        $links .= "<a href=\"{$url}&page={$total_pages}\">{$total_pages}</a>";
    }
    return $links;
}