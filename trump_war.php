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
$gameManeger = new GameManeger($player,$deck,$hand);
$gameManeger->gameContinue();



//final result
for($i = 1; $i<=$playerNumber; $i++){
    $totalCards = count($playerHands[$i])+count($winCards[$i]);//手札と勝札の合算
echo "{$playerNames[$i-1]}の手札の枚数は{$totalCards}枚です。\n";
//人数分ループ
}

$playerTotals  =[];//プレイヤーごとの合計枚数
for($i = 1; $i<=$playerNumber; $i++){
    $totalCards = count($playerHands[$i])+count($winCards[$i]);
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