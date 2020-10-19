<?php

namespace Func;

use Elliptic\EC;
use kornrunner\Keccak;
use BIP\BIP44;
use BitWasp\BitcoinLib\BIP39\BIP39;
use Ethereum\Ethereum;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;
use Web3\Utils;
use Web3\Web3;
use Web3p\EthereumTx\Transaction;
use Web3p\EthereumUtil\Util;
use EthTool\KeyStore;
use EthTool\Credential;
use EthTool\Callback;
use GuzzleHttp\Client;

require_once(RootPath.'/Vendor/autoload.php');

class Ethlib
{

    protected static $eth_url = 'https://mainnet.infura.io/v3/8b472f30c4cc41c9a530faa562b9de7f';
    protected static $eth = null;
    protected static $personal = null;
    public static $web3 = null;
    /**
     * @var Ethereum
     */
    public static $new_web = null;

    /*
    * 单例获取eth实例
    */
    public static function getWeb3()
    {
        if (empty(self::$web3)) {
            // 30 Second timeout
            $timeout = 30;

            self::$web3 = new Web3(new HttpProvider(new HttpRequestManager(self::$eth_url, $timeout)));
            //self::$web3 = new Web3(self::$eth_url);
        }
        return self::$web3;
    }

    /*
     * 单例获取eth实例
     */
    public static function getEth()
    {
        if (empty(self::$eth)) {
            $web3 = self::getWeb3();
            self::$eth = $web3->getEth();
        }
        return self::$eth;
    }

    /*
     * 导入后助记词 还原钱包
     */
    public static function getWalletByMnemonic($word, $callback)
    {
        $seed = BIP39::mnemonicToSeedHex($word, '');
        $HDKey = BIP44::fromMasterSeed($seed)->derive("m/44'/60'/0'/0/0");
        $util = new Util();
        //生成私钥
//      var_dump('0x' . $HDKey->privateKey);
        $pub = $util->privateKeyToPublicKey($HDKey->privateKey);
        $address1 = $util->publicKeyToAddress($pub);
        if (!Utils::isAddress($address1)) {
            $callback(0, '助记词不正确');
            return;
        }
        $callback([
            'code' => 1,
            'eth_address' => $address1,
            'eth_private' => '0x' . $HDKey->privateKey,
            'eth_seed' => $seed,
        ]);
        return;
    }

    /* 查询钱包的余额 */
    public static function getAddressBalance($address='', $callBack) {
        $eth = self::getEth();
        $eth->getBalance($address, function ($err, $data) use($callBack) {
            if ($err !== null) {
                echo 'Error: ' . $err->getMessage();
                return;
            }
            list($bnq, $bnr) = Utils::fromWei($data, 'gwei');
            $balance = $bnq->toString() / 1000000000;
            if($callBack) {
                $callBack($balance);
            }
        });
    }

    /*
     * 导入私钥 还原钱包
     */
    public static function getWalletByPrivateKey($privateKey)
    {

        $ec = new EC('secp256k1');
        $keyPair = $ec->keyFromPrivate($privateKey);
        $privateKey = $keyPair->getPrivate()->toString(16,2);
        $pubKey = $keyPair->getPublic()->encode('hex');
        $address = '0x' . substr(Keccak::hash(substr(hex2bin($pubKey), 1), 256), 24);
        return [
            'privateKey' => $privateKey,
            'address' => $address,
            'pubKey' => $pubKey,
        ];
    }

    /*
     * 单例获取eth实例
     */
    public static function getPersonal()
    {
        if (empty(self::$personal)) {
            $web3 = self::getWeb3();
            self::$personal = $web3->getPersonal();
        }
        return self::$personal;
    }
    /*
     * 创建助记词
     */
    protected static function _createMnemonic()
    {
        return BIP39::entropyToMnemonic(BIP39::generateEntropy(128));
    }

    /*
     * 创建钱包
     */
    public static function createWalletByMnemonic($mnemonic='')
    {
        //Get private key by path and seed
        $mnemonic = $mnemonic ? : self::_createMnemonic();
        $seed = BIP39::mnemonicToSeedHex($mnemonic, '');
        $HDKey = BIP44::fromMasterSeed($seed)->derive("m/44'/60'/0'/0/0");
        $util = new Util();
        $pub = $util->privateKeyToPublicKey($HDKey->privateKey);
        $address1 = $util->publicKeyToAddress($pub);
        return [
            'mnemonic' => $mnemonic,
            'eth_address' => $address1,
            'eth_private' => '0x' . $HDKey->privateKey,
            'eth_seed' => $seed,
        ];
    }


    private static  function getTransactionCount($from,$callback)
    {
        $eth = self::getEth();
        $eth->getTransactionCount($from, function ($err, $data) use ($eth, $callback) {
            if ($err !== null) {
                $callback(0, $err->getMessage());
            } else {
                $callback(1, $data->toString());
            }

        });
    }
    //发送签名交易 原文
    public static function sendRawTransOld($privateKey, $fromAccount, $toAccount,$amount,$callback){
        $eth = self::getEth();
        self::getTransactionCount($fromAccount, function ($code,$nonce) use ($fromAccount,$toAccount,$amount, $eth,$privateKey, $callback) {
            if($code==0){
                $callback(0, $nonce);
            }else{
                $bnq = Utils::toWei($amount,'ether')->toString();
//                $gasPrice = '0x' . Utils::toWei('33', 'gwei')->toHex();
                $gasPrice = '0x' . Utils::toWei('20', 'gwei')->toHex();
                $raw=[
                    'from' => $fromAccount,
                    'to' => $toAccount,
                    'value' => Utils::toHex($bnq, true),
                    //'gas' => Utils::toHex(90000, true),
                    'gasLimit'    => '0x76c0',
                    'gasPrice' => $gasPrice,//Utils::toHex(33 * 1000000000, true),
                    'chainId'=>1,
                    'nonce'=> Utils::toHex($nonce, true),
                ];
                $txreq = new Transaction($raw);
                $signed = '0x' . $txreq->sign( '0x' .$privateKey);
                $eth->sendRawTransaction($signed, function ($err, $transaction) use ($eth, $callback) {
                    if ($err !== null) {
                        if ($err->getMessage() == 'insufficient funds for gas * price + value') {
                            $callback(0, '账户余额不足');
                        } else {
                            $callback(0, $err->getMessage());
                        }

                        return;
                    }
                    $callback(1, $transaction);
                });
            }
        });
    }
    //发送本地交易
    public static function sendTransOld($fromAccount, $toAccount, $password, $amount, $callback)
    {
        $eth = self::getEth();
        self::getPersonal()->unlockAccount($fromAccount, $password,
            function ($err, $unlocked) use ($eth, $fromAccount, $toAccount, $callback, $amount) {
                if ($err !== null) {
                    if ($err->getMessage() == 'could not decrypt key with given passphrase') {
                        $callback(0, '钱包密码不正确');
                    } else {
                        $callback(0, $err->getMessage());
                    }
                    return;
                }
                if ($unlocked) {
                    //$bnq = $amount * 1000000000;
                    $bnq = Utils::toWei($amount,'ether')->toString();
                    //$callback(0,Utils::toHex($bnq->toString(),true).'---'.$amount);
                    //return;
                    $eth->sendTransaction([
                        'from' => $fromAccount,
                        'to' => $toAccount,
                        'value' => Utils::toHex($bnq, true),
                        'gas' => Utils::toHex(90000, true),
                        'gasPrice' => Utils::toHex(33 * 1000000000, true),
                    ], function ($err, $transaction) use ($eth, $fromAccount, $toAccount, $callback) {
                        if ($err !== null) {
                            if ($err->getMessage() == 'insufficient funds for gas * price + value') {
                                $callback(0, '账户余额不足');
                            } else {
                                $callback(0, $err->getMessage());
                            }

                            return;
                        }
                        $callback(1, $transaction);
                    });
                } else {
                    $callback(0, '钱包密码不正确');
                }
            });
    }

    public static function syncOrder($address, $start_block, $end_block)
    {

    }

    //发送签名交易 改版
    public static function sendRawTrans($privateKey, $fromAccount, $toAccount,$amount,$callback){
        $eth = self::getEth();
        self::getTransactionCount($fromAccount, function ($code,$nonce) use ($fromAccount,$toAccount,$amount, $eth,$privateKey, $callback) {
            if($code==0){
                $callback(0, $nonce);
            }else{
                $bnq = Utils::toWei($amount,'ether')->toString();
//                $gasPrice = '0x' . Utils::toWei('33', 'gwei')->toHex();
                $gasPrice = '0x' . Utils::toWei('22', 'gwei')->toHex();
                $raw=[
                    'from' => $fromAccount,
                    'to' => $toAccount,
                    'value' => Utils::toHex($bnq, true),
                    //'gas' => Utils::toHex(90000, true),
                    'gasLimit'    => '0x76c0',
                    'gasPrice' => $gasPrice,//Utils::toHex(33 * 1000000000, true),
                    'chainId'=>1,
                    'nonce'=> Utils::toHex($nonce, true),
                ];
                //生成 keystore
                KeyStore::save($privateKey,'ygmlxomlg','./keystore');
                $credential = Credential::fromWallet('ygmlxomlg','./keystore/'. str_replace('0x', '', $fromAccount) .'.json');
                $signed = $credential->signTransaction($raw, $privateKey);
                $eth->sendRawTransaction($signed, function ($err, $txnHash) use ($eth, $callback) {
                    if ($err !== null) {
                        if ($err->getMessage() == 'insufficient funds for gas * price + value') {
                            $callback(0, '账户余额不足');
                        } else {
                            $callback(0, $err->getMessage());
                        }

                        return;
                    }
                    $callback(1, $txnHash);
                });
            }
        });
    }

    //发送签名交易 改版
    public static function checkPayStatus($txnHash,$callback){
//      $txnHash = preg_replace("/^0x/", '', $txnHash);
        $eth = self::getEth();
        $eth->getTransactionReceipt($txnHash, $callback);
    }


    /* 查询钱包的交易记录 */
    public static function getAddressTransactions($address='', $callBack) {

      
//        $res = file_get_contents($host);
//        $res_json = json_decode($res, true);
//        $callBack($res_json);
    }
}

