<?php
//カードの設定
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
        $values =[14=>"A",13=>"K",12=>"Q",11=>"J",10=>10,9=>9,8=>8,7=>7,6=>6,5=>5,4=>4,3=>3,2=>2];
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
        $this->shuffleDeck();
    }
    public function createDeck(){
        for( $suit =0; $suit<4; $suit++ ){
            for($value=2;$value<=14;$value++){
                $this->cards[]=new Card($suit,$value);
            }
        }
    }
    public function shuffleDeck(){
        shuffle($this->cards);
    }
    public function getCards(){
        return $this->cards;
    }
}
//プレイヤー設定
class Player{
    private $playerNumber;
    private $playerNames = [];

    public function __construct(){
        $this->playerNumber = $this->setPlayerNumber();
        $this->setPlayerName();
    }
    //人数設定
    public function setPlayerNumber(){
        echo "プレイヤーの人数を入力してください（2〜5）:";
        $playerNumber = (fgets(STDIN));
        //不正な値の時
        while($playerNumber <2 || $playerNumber > 5){
        echo "エラー。人数は2～5で指定してください:";
        $playerNumber = (fgets(STDIN));
        }
        return (int)$playerNumber;
    }
    
    //プレイヤーネーム設定
    
    public function setPlayerName(){
        for($i=1;$i<=$this->playerNumber;$i++){
            echo "プレイヤー{$i}の名前を入力してください:";
            $playerName =trim(fgets(STDIN));
            if($playerName === ""){ 
                $playerName = "プレイヤー{$i}"; //入力なし:デフォルトのプレイヤーネームを設定
            }
        $this->playerNames[] =$playerName;
        }
    }

    public function getPlayerNumber(){
        return $this->playerNumber;
    }
    public function getPlayerNames(){
        return $this->playerNames;
    }
}
//手札の設定
class Hand{
    private $cards;//山札
    private $player;
    private $playerCards;//プレイヤーごとの手札
    public function __construct($cards,$player){
        $this->cards = $cards;
        $this->player = $player;
        $this->playerCards = [];
        $this->setHands();
        $this->openHands();
    }
    public function setHands(){
        $playerNumber = $this->player->getPlayerNumber();
        for($i= 1;$i<=$playerNumber;$i++){
            $this->playerCards[$i] =[]; //人数分手札初期化
        }
        $nowPlayer =1;
        while(count($this->cards) > 0){
            $this->playerCards[$nowPlayer][] = array_pop($this->cards); 
            $nowPlayer++;
            //プレイヤー1に戻す
            if($nowPlayer > $playerNumber){
                $nowPlayer =1;
            }
        }
    }
    public function openHands(){ //手札公開(動作確認用)
        $playerNumber = $this->player->getPlayerNumber();
        $playerNames = $this->player->getPlayerNames();
        for( $i= 1;$i<=$playerNumber;$i++){
            echo "{$playerNames[$i-1]}.\n";
            foreach($this->playerCards[$i] as $playerCard){
                echo $playerCard->cardInfo()."\n";
            }
        }
    }
    public function getPlayerCards(){
        return $this->playerCards;
    }

}
class GameManeger{
    private 
}