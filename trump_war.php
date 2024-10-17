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
//結果発表
$playerHands =$gameManeger->getPlayerHands();
$winCards= $gameManeger->getWinCards();
$result = new Result($playerHands, $winCards,$playerNames);
echo "戦争を終了します。";