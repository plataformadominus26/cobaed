<?php
    function getMascot(){
        $kobaiDir=__DIR__."/../assets/img/kobai";
        $randomPng=scandir($kobaiDir);
        $randomPng = array_filter(scandir($kobaiDir), function($file) {
            return preg_match('/\.(png|jpg|jpeg|gif)$/i', $file);
        });
        $randomIndex=array_rand($randomPng);
        $img = "https://" . $_SERVER['HTTP_HOST'] . "/cobaed/assets/img/kobai/" . $randomPng[$randomIndex];

                // Convert filesystem path to public URL
                $publicUrl = str_replace(__DIR__ . '/../', '../', $img);
            return $img;
        
    }

?>