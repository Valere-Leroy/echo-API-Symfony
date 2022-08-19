<?php

namespace App\DataFixtures;

class TagProvider extends \Faker\Provider\Base
{
    protected static $tags = [
        'Blues',
        'Chanson française',
        'Drum and bass',
        'Easy listening',
        'Electronic dance music',
        'Electronica',
        'Funk',
        'Gospel',
        'Heavy metal',
        'Jazz',
        'Musique classique', 
        'Country', 
        'Musique électronique', 
        'Musique expérimentale', 
        'Musique folk', 
        'Instrumentale', 
        'Musique latine', 
        'Musique soul', 
        'Musiques du monde', 
        'New age', 
        'Pop', 
        'Rap', 
        'Reggae', 
        'RnB contemporain', 
        'Rock', 
        'Dubstep' 
    ];

    public static function tagTitle(){
        return static::randomElement(static::$tags);
    }
}