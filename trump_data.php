<?php
class Card{
    private $suit;
    private $value;

    public function __construct($suit,$value){
        $this->suit = $suit;
        $this->value = $value;
    }
//カードの強さ設定
//スート分け
    public function cardInfo(){
        $suits =["スペード","ダイヤ","ハート","クラブ"];
        $values =[14=>"A",13=>"K",12=>"Q",11=>J,10=>10,9=>9,8=>8,7=>7,6=>6,5=>5,4=>4,3=>3,2=>2];
        return "{$suits[$this->suit]}の{$values[$this->value]}";
    }
    public function getSuit(){
        return $this->suit;
    }
    public function getValue(){
        return $this->value;
    }
}
//山札の設定
class Deck{
    private $cards=[];
    public function __construct(){
        $this->createDeck();
    }
    public function createDeck(){
        for( $suit =0; $suit<4; $suit++ ){
            for($value=2;$value<=14;$value++){
                $this->cards[]=new Card($suit,$value);
            }
        }
    }
//カードの配布
    public function drawCard(){
        if(count($this->cards)> 0){
            $drawnCard = $this->cards[array_rand($this->cards)];
            return $drawnCard;
        }

    }
}