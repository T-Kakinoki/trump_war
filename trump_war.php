<?php
//実際のゲーム画面の挙動を記述
require_once("trump_data.php");

//game start
echo "戦争を開始します。";
//add user
echo "プレイヤーの人数を入力してください（2〜5）:";
$playerNumber = (fgets(STDIN));
//不正な値の時
while($playerNumber <2 || $playerNumber > 5){
    echo "エラー。人数は2～5で指定してください";
    $playerNumber = (fgets(STDIN));
}
//add userで入力された数だけループ,プレイヤー名設定
//入力なしの時　　プレイヤー名をデフォルトで設定
for($i=1;$i<=$playerNumber;$i++){
    echo "プレイヤー{$i}の名前を入力してください:";
    $playerName =(fgets(STDIN));
    if($playerName === "\n"){
        $playerName = "プレイヤー{$i}";
    }
$playerNames[] =$playerName;
}
//山札の生成
$deck =new Deck();

//ランダムでのカード配布
$playerCards =[];
for($i= 1;$i<=$playerNumber;$i++){
    $playerCards[$i] =[];
}
$nowPlayer =1;
while(count($deck->getCards()) > 0){
    $playerCards[$nowPlayer][] = $deck->drawCard(); 
    $nowPlayer++;
//プレイヤー1に戻す
if($nowPlayer > $playerNumber){
    $nowPlayer =1;
}
}
echo "カードが配られました。";//配布完了

//手札の公開(動作確認用)
for( $i= 1;$i<=$playerNumber;$i++){
    echo "{$playerNames[$i-1]}.\n";
    foreach($playerCards[$i] as $playerCard){
        echo $playerCard->cardInfo()."\n";
    }
}

//game start
$gameContinue = true;

while($gameContinue){
    echo "戦争！"; //ゲームのループ開始地点はここ
    $battleCards =[]; //場のカード
    $cardValues =[]; //カードの強さ
    $stockCards =[]; //引き分け時保管
    $winCards =[]; //勝利時保管
//手札からランダムに一枚出す
    for($i= 1;$i<=$playerNumber;$i++){
        $battleCardIndex = array_rand($playerCards[$i]);
        $battleCard = $playerCards[$i][$battleCardIndex];
//場に出たカードの一時保存
        $battleCards[$i] = $battleCard;
        $cardValues[$i] = $battleCard->cardInfo();

//出したカードを手札から削除
    unset($playerCards[$i][$battleCardIndex]);

//card open    
    echo "{$playerNames[$i-1]}のカードは{$battleCard->cardInfo()}です";
    }
//battle
//カードの値を比較、最大値をピックアップ
    $maxCardValue = max($cardValues);
//勝利プレイヤーの特定
    $winner = array_keys($cardValues,$maxCardValue); //勝者決定
//引き分けの時(勝者が二名以上いるとき) 
    if(count($winner)>1){
        echo "引き分けです";
        foreach($winner as $stock){
            $stockCards[] = $battleCards[$stock];//場に出ていたカードをストックへ
        }continue;//再戦
    }else{ 
//勝者が確定した場合
    echo "勝ちプレイヤーが勝ちました。勝ちプレイヤーはカードを○枚もらいました。";
//前回等に引き分けた際キャリーオーバーする
//勝者も$stockCardsにいったん格納後総取りする

    }
    





//next game
//game startに戻る


//game set
echo "手札0プレイヤーの手札がなくなりました。";
}


//final result
echo "(プレイヤー名)の手札の枚数は○○枚です。";
//順位上から人数分ループ

echo "プレイヤー1が1位、プレイヤー2が2位です。";
//順位上から人数分ループ
//これ最下位だけ文末が｢です。｣になってるから要注意
echo "戦争を終了します。";
?>



//#ジョーカー
//#スペードA
//いったん後回し