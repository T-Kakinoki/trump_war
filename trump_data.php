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
    private $playerHands;//プレイヤーごとの手札
    public function __construct($cards,$player){
        $this->cards = $cards;
        $this->player = $player;
        $this->playerHands = [];
        $this->setHands();
        $this->openHands();
    }
    public function setHands(){
        $playerNumber = $this->player->getPlayerNumber();
        for($i= 1;$i<=$playerNumber;$i++){
            $this->playerHands[$i] =[]; //人数分手札初期化
        }
        $nowPlayer =1;
        while(count($this->cards) > 0){
            $this->playerHands[$nowPlayer][] = array_pop($this->cards); 
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
            foreach($this->playerHands[$i] as $playerCard){
                echo $playerCard->cardInfo()."\n";
            }
        }
    }
    public function getPlayerHands(){
        return $this->playerHands;
    }

}
//ゲーム基礎部分
class GameManeger{
    private $battleCards = [];//場に出すカード
    private $stockCards = [];//一時保存場所
    private $winCards =[];//勝札
    private $cardValues = [];
    private $aces =[];
    private $players;
    private $deck;
    private $hand;
    private $playerNumber;
    private $playerNames;
    private $playerHands;


    public function __construct($players,$deck,$hand){
        $this ->players =$players;
        $this ->deck = $deck;
        $this ->hand =$hand;
        $this ->playerNumber = $this -> players->getPlayerNumber();
        $this->playerNames = $this -> players->getPlayerNames();
        $this->playerHands = $this ->hand-> getPlayerHands();
    }
    //ゲーム継続条件
    public function gameContinue(){
        $gamecontinue =true;
        $gamecontinue = $this->checkEmpty();//手札の空チェック
        while($gamecontinue){
            echo "戦争！\n";
            $this->openCard(); //カードを場へ
            //勝敗分岐
            $this->judgeGame();


            $gamecontinue = $this->checkEmpty();//再度手札の空チェック
        }

    }
    //手札ゼロのプレイヤーがいるかチェック
    public function checkEmpty(){
        for($i= 1;$i<=$this->playerNumber;$i++){
            //手札なし、勝札なし
            if(empty($this->playerHands[$i]) && empty($this->winCards[$i])){
                echo "{$this->playerNames[$i-1]}の手札がなくなりました。\n";
                return false; //ゲーム終了、リザルトへ
            }
            //手札なし、勝札あり
            if(empty($this->playerHands[$i]) && !empty($this->winCards[$i])){
                $this->playerHands[$i] = $this->winCards[$i]; //勝札を手札へ
                shuffle($this->playerHands[$i]); //シャッフル
                $this->winCards[$i] = []; //それまでの勝札をリセット
            }
        }
        return true; //ゲーム継続
    }

    //手札から場にカードを出す
    public function openCard(){
        $this->cardValues =[];
        $this->aces =[];
        for($i= 1;$i<=$this->playerNumber;$i++){
            $battleCard = array_pop($this->playerHands[$i]);//山札から場へ
            $this->battleCards[$i] = $battleCard;
            $this->cardValues[$i] = $battleCard->getValue();//カード情報の一時保存
            if($this->cardValues[$i] == 14){
                $this->aces[] = $i;
            }
            echo "{$this->playerNames[$i-1]}のカードは{$battleCard->cardInfo()}です。\n";//場に出したカードの宣言
        }
        
    }
    //勝敗判定
    public function judgeGame(){

        $maxCardValue = max($this->cardValues);//カードの最大値ピックアップ
        $winner = array_keys($this->cardValues,$maxCardValue); //勝者決定
        $winnerIndex =$winner[0];
        if(count($winner)>1 && $maxCardValue===14 && count($this->aces)> 1){ //引き分けかつAが二枚以上
            $this->extrawin($winnerIndex); //特殊勝利

        }elseif(count($winner)> 1){
            $this->draw(); //通常引き分け
        }else{
            $this->win($winnerIndex); //勝利
        }
    }
    //各勝敗判定
    public function win($winnerIndex) { //勝利時
            foreach($this->battleCards as $battleCard){
                $this->stockCards[] = $battleCard;
            }
            $countCard = count($this->stockCards);
            echo "{$this->playerNames[$winnerIndex-1]}が勝ちました。{$this->playerNames[$winnerIndex-1]}はカードを{$countCard}枚もらいました。\n";
            foreach($this->stockCards as $stockCard){
                $this->winCards[$winnerIndex][] = $stockCard;//$stockcardsの中身をすべて勝札へ
            }
            $this->stockCards = [];//ストックのリセット  
    }
    public function draw(){ //引き分け時
        echo "引き分けです。\n";
                foreach($this->battleCards as $battleCard){
                    $this->stockCards[] = $battleCard;//場に出ていたカードをストックへ
                }
    }
    public function extrawin($winnerIndex){ //スペードＡ特殊勝利
        $countCard = count($this->stockCards);
        foreach($this->stockCards as $stockCard){
            $this->winCards[$winnerIndex][] = $stockCard;
        }//$stockcardsの中身をすべて代入
        $this->stockCards = [];//ストックのリセット
        echo "世界一！\n";
        echo "{$this->playerNames[$winnerIndex-1]}が勝ちました。{$this->playerNames[$winnerIndex-1]}はカードを{$countCard}枚もらいました。\n";
    }




}

