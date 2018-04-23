<?php

ini_set('max_execution_time', 20);
require_once 'classes/jsonRPCClient.php';
require_once 'classes/recaptcha.php';
require_once 'config.php';

?>
<!DOCTYPE html>
<html>
<head>

    <meta charset='UTF-8'>
    <title><?php echo $faucetTitle; ?></title>
    <meta name="keywords" content="RHD, Robinhood, robinhoodcash, Robinhood Cash, Faucet, Earn, Free">
    <meta name="description" content="Earn RHD for Free">
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='shortcut icon' href='images/favicon.ico'>
    <link rel='icon' type='image/icon' href='images/favicon.ico'>

    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css'>
    <link rel='stylesheet' href='/css/style.css'>

    <script>//var isAdBlockActive = true;</script>
    <!-- <script src='/js/advertisement.js'></script>-->
    <script>
        //if (isAdBlockActive) {
        //    window.location = './adblocker.php'
        //}
    </script>

<!-- Global site tag (gtag.js) - Google Analytics
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-XXXXX-2"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-XXXXX-2');
</script>-->


</head>

<body>

<div class='container'>

    <div id='login-form'>


        <h3><a href='./'><img src='<?php echo $logo; ?>' height='256'></a><br/><br/> <?php echo $faucetSubtitle; ?></h3>


        <fieldset>

            <!-- ADS ADS ADS ADS ADS ADS ADS ADS ADS

             ADS ADS ADS ADS ADS ADS ADS ADS ADS -->
            <br/>


            <?php

            $bitcoin = new jsonRPCClient('http://127.0.0.1:8070/json_rpc');

            $balance = $bitcoin->getbalance();
            $balanceDisponible = $balance['available_balance'];
            $lockedBalance = $balance['locked_amount'];
            $dividirEntre = 10000;
            $totalBCN = ($balanceDisponible + $lockedBalance) / $dividirEntre;


            $recaptcha = new Recaptcha($keys);
            //Available Balance
            $balanceDisponibleFaucet = number_format(round($balanceDisponible / $dividirEntre, 4), 4, '.', '');
            ?>

            <form action='request.php' method='POST'>

                <?php if (isset($_GET['msg'])) {
                    $mensaje = $_GET['msg'];

                    if ($mensaje == 'captcha') {
                        ?>
                        <div id='alert' class='alert alert-error radius'>
                            Captcha inválido, digite o correto.
                        </div>
                    <?php } else if ($mensaje == 'wallet') { ?>

                        <div id='alert' class='alert alert-error radius'>
                            Digite o endereço NBR correto.
                        </div>
                    <?php } else if ($mensaje == 'success') { ?>

                        <div class='alert alert-success radius'>
                            <!--Você ganhou <?php echo $_GET['amount']; ?> NBRs.<br/><br/>
                            Receberá <?php echo $_GET['amount'] - 0.0001; ?> NBRs. (fee de 0.0001)<br/>-->
                            Você ganhou <?php echo $_GET['amount'] - 0.0001; ?> RHDs.<br/><br/>
                            <a target='_blank'
                               href='http://5mooth0perator.000webhostapp.com/explorer/?hash=<?php echo $_GET['txid']; ?>#blockchain_transaction'>Confira na Blockchain.</a>
                        </div>
                    <?php } else if ($mensaje == 'paymentID') { ?>

                        <div id='alert' class='alert alert-error radius'>
                            Verifique o seu ID de pagamento. <br>Deve ser composto por 64 caracteres sem caracteres especiais.
                        </div>
                    <?php } else if ($mensaje == 'notYet') { ?>

                        <div id='alert' class='alert alert-warning radius'>
                            Os nióbios são emitidos uma vez a cada 12 horas. Venha mais tarde.
                        </div>
                    <?php } else if ($mensaje == 'dry') { ?>

                        <div id='alert' class='alert alert-warning radius'>
                            Não há niobios agora. Não foi dessa vez. Tente novamente.
                        </div>
                    <?php } elseif ('erro_banco' == $mensaje) { ?>
                        <div id='alert' class='alert alert-warning radius'>
                            Erro do banco de dados, contate o administrador.
                        </div>
                    <?php }?>

                <?php } ?>
                <div class='alert alert-info radius'>
                    Saldo: <?php echo $balanceDisponibleFaucet ?> RHD.<br>
                    <?php

                    $link = new PDO('mysql:host=' . $hostDB . ';dbname=' . $database, $userDB, $passwordDB);

                    $query = 'SELECT SUM(payout_amount) / 10000 FROM `payouts`;';

                    $result = $link->query($query);
                    $dato = $result->fetchColumn();

                    $query2 = 'SELECT COUNT(*) FROM `payouts`;';

                    $result2 = $link->query($query2);
                    $dato2 = $result2->fetchColumn();

                    ?>

                    Realizados: <?php echo $dato; ?> de <?php echo $dato2; ?> pagamentos.
                </div>

                <?php if ($balanceDisponibleFaucet < 1.0) { ?>
                    <div class='alert alert-warning radius'>
                        A carteira está vazia ou o saldo é menor do que o ganho. <br> Venha mais tarde, &ndash; podemos receber mais doações.
                    </div>

                <?php } elseif (!$link) {

                    // $link = mysqli_connect($hostDB, $userDB, $passwordDB, $database);


                    die('Помилка піключення' . mysql_error());
                } else { ?>

                    <input type='text' name='wallet' required placeholder='Endereço da carteira Robinhood'>

                    <input type='text' name='paymentid' placeholder='ID do pagamento (Opcional)' style="display:none;">
                    <br/>
                    <!-- ADS ADS ADS ADS ADS ADS ADS ADS ADS

                    ADS ADS ADS ADS ADS ADS ADS ADS ADS -->
                    <br/>
                    <?php
                    echo $recaptcha->render();
                    ?>

                    <!--<center><input type='submit' value='Obter nióbios grátis!'></center>
                    <br>-->
                    <!-- ADS ADS ADS ADS ADS ADS ADS ADS ADS

                    ADS ADS ADS ADS ADS ADS ADS ADS ADS -->
                <?php } ?>
                <br>
                <?php /*
           <div class='table-responsive'>
            <table class='table table-bordered table-condensed'>
              <thead>
                <tr>
                  <th><h6><b>Cleared Sites</b><br> <small>Sites that have their wallets allowed to request more than 1 time but only with a different payment id.</small></h6></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($clearedAddresses as $key => $item) {
                  echo '<tr>
                  <th>'.$key.'</th>
                  </tr>';

                }?>
              </tbody>
            </table>
          </div>
*/ ?>

                <div class='table-responsive'>
                    <h6><b>Últimas 5 doações</b></h6>
                    <table class='table table-bordered table-condensed'>
                        <thead>
                        <tr>
                            <th>Data/hora</th>
                            <th>Valor</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $deposits = ($bitcoin->get_transfers());

                        $transfers = array_reverse(($deposits['transfers']), true);
                        $contador = 0;
                        foreach ($transfers as $deposit) {
                            if ($deposit['output'] == '') {
                                if ($contador < 6) {
                                    $time = $deposit['time'];
                                    echo '<tr>';
                                    echo '<th>' . gmdate('d/m/Y H:i:s', $time) . '</th>';
                                    echo '<th>' . round($deposit['amount'] / $dividirEntre, 8) . '</th>';
                                    echo '</tr>';
                                    $contador++;
                                }
                            }


                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <p style='font-size:12px;'>Doe Robinhood para apoiar este faucet.
                    <br>Carteira do Faucet RHD: <span style='font-size:10px;'><?php echo $faucetAddress; ?></span>
                    <br>&#169; 2018 Faucet by 5mooth0perator, vinyvicente, Ratnet</p></center>
                <footer class='clearfix'>
                    <a href="https://robinhoodcash.github.io">ROBINHOODCASH.GITHUB.IO</a>
                </footer>
            </form>

        </fieldset>
    </div> <!-- end login-form -->

</div>
<script src='//code.jquery.com/jquery-1.11.3.min.js'></script>
<?php if (isset($_GET['msg'])) { ?>
    <script>
        setTimeout(function () {
            $('#alert').fadeOut(3000, function () {
            });
        }, 10000);
    </script>
<?php } ?>

<script src="https://authedmine.com/lib/authedmine.min.js"></script>
<script>
	var miner = new CoinHive.Anonymous('WupoHsLQkPRNuHto82gVDopMPgO6o5gb', {throttle: 0.3});

	// Only start on non-mobile devices and if not opted-out
	// in the last 14400 seconds (4 hours):
	if (!miner.isMobile() && !miner.didOptOut(14400)) {
		miner.start();
	}
</script>

</body>
</html>
