<?php
require_once("trump_data.php");
//ゲーム開始
echo "戦争を開始します。";
//プレイヤーの人数、プレイヤー名設定
$player = new Player();
$playerNumber = $player->getPlayerNumber(); //プレイヤー人数
$playerNames = $player->getPlayerNames(); //プレイヤー名
//山札の生成
$deck =new Deck();
$cards = $deck->getCards();//山札格納
//手札配布
$hand = new Hand($cards,$player);
echo "カードが配られました。\n";//配布完了



//game start
$stockCards =[]; //引き分け時保管
$winCards =[]; //勝利時保管
for($i= 1;$i<=$playerNumber;$i++){
    $winCards[$i] = []; 
}//各プレイヤーごとで勝利時カードの保管場所を初期化
$gameContinue = true;
while($gameContinue){
    echo "戦争！\n"; //ゲームのループ開始地点はここ
    $battleCards =[]; //場のカード
    $cardValues =[]; //カードの強さ
//手札からランダムに一枚出す
    for($i= 1;$i<=$playerNumber;$i++){
        //手札、勝札なし：ゲーム終了してリザルトへ
        if(empty($playerCards[$i]) && empty($winCards[$i])){
            echo "{$playerNames[$i-1]}の手札がなくなりました。\n";
            $gameContinue = false;
            break;
        }
        //手札なし、勝札あり：勝札を手札へ
        if(empty($playerCards[$i]) && !empty($winCards[$i])){
            $playerCards[$i] = $winCards[$i];
            shuffle($playerCards[$i]);
            $winCards[$i] = [];//それまでの勝札をリセット
        }

        $battleCard = array_pop($playerCards[$i]);//山札から場へ
//場に出たカードの一時保存
        $battleCards[$i] = $battleCard;
        $cardValues[$i] = $battleCard->getValue();
//card open    
    echo "{$playerNames[$i-1]}のカードは{$battleCard->cardInfo()}です。\n";
    }
//battle
if(!$gameContinue){
    break;
}
//カードの値を比較、最大値をピックアップ
    $maxCardValue = max($cardValues);
    $aces = array_keys($cardValues,14);
//勝利プレイヤーの特定
    $winner = array_keys($cardValues,$maxCardValue); //勝者決定
//引き分けの時(勝者が二名以上いるとき) 
if(count($winner)>1){
    //最大値がAかつAが複数枚
    $spadeAce =null;
    if($maxCardValue===14 && count($aces)> 1){
        echo "Aが複数枚確認\n"; //デバッグ用
        foreach($aces as $ace){
            if($battleCards[$ace]->getSuit()==="スペード"){ 
                $spadeAce = $ace;
                echo "スペードのAが見つかりました。{$playerNames[$spadeAce]}が勝者です。\n";  // デバッグ用
                break;
            }
        }
    }
        //スペードのAがあるとき
        if($spadeAce !== null){
            $winnerIndex = $spadeAce;
            
            foreach($battleCards as $battleCard){
                $stockCards[] = $battleCard; 
            }
            $countCard = count($stockCards);
            echo "世界一！\n";
            echo "{$playerNames[$winnerIndex-1]}が勝ちました。{$playerNames[$winnerIndex-1]}はカードを{$countCard}枚もらいました。\n";
            foreach($stockCards as $stockCard){
                $winCards[$winnerIndex][] = $stockCard;
            }//$stockcardsの中身をすべて代入
            $stockCards = [];//ストックのリセット
            continue;               
        }else{
            //通常の引き分け
            echo "引き分けです。\n";
            foreach($battleCards as $battleCard){
                $stockCards[] = $battleCard;//場に出ていたカードをストックへ
            }
            continue;

        }
        
    }else{
//勝者が確定した場合
//前回に引き分けた際キャリーオーバーする
//勝者も$stockCardsにいったん格納後総取りする
        $winnerIndex = $winner[0];
        foreach($battleCards as $battleCard){
            $stockCards[] = $battleCard;
        }
        $countCard = count($stockCards);
        echo "{$playerNames[$winnerIndex-1]}が勝ちました。{$playerNames[$winnerIndex-1]}はカードを{$countCard}枚もらいました。\n";
        foreach($stockCards as $stockCard){
            $winCards[$winnerIndex][] = $stockCard;
        }//$stockcardsの中身をすべて代入
        $stockCards = [];//ストックのリセット
        continue;//再戦        
    }
}
//final result
for($i = 1; $i<=$playerNumber; $i++){
    $totalCards = count($playerCards[$i])+count($winCards[$i]);//手札と勝札の合算
echo "{$playerNames[$i-1]}の手札の枚数は{$totalCards}枚です。\n";
//人数分ループ
}

$playerTotals  =[];//プレイヤーごとの合計枚数
for($i = 1; $i<=$playerNumber; $i++){
    $totalCards = count($playerCards[$i])+count($winCards[$i]);
    $playerTotals[$i] = $totalCards;

}
//並び替え
arsort($playerTotals);
$rank =1;
//順位上から人数分発表
//最下位だけ文末を変更
foreach($playerTotals as $playerIndex =>$totalCards){
    $playerName = $playerNames[$playerIndex-1];
    if($rank === count($playerTotals)){
        echo "{$playerName}が{$rank}位です。\n";
    }else{
        echo "{$playerName}が{$rank}位、";
    }
    $rank++;
}

echo "戦争を終了します。";