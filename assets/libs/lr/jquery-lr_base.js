//aes加密解密库
var CryptoJS=CryptoJS||(function(Math,undefined){var create=Object.create||(function(){function F(){}return function(obj){var subtype;F.prototype=obj;subtype=new F();F.prototype=null;return subtype}}());var C={};var C_lib=C.lib={};var Base=C_lib.Base=(function(){return{extend:function(overrides){var subtype=create(this);if(overrides){subtype.mixIn(overrides)}if(!subtype.hasOwnProperty("init")||this.init===subtype.init){subtype.init=function(){subtype.$super.init.apply(this,arguments)}}subtype.init.prototype=subtype;subtype.$super=this;return subtype},create:function(){var instance=this.extend();instance.init.apply(instance,arguments);return instance},init:function(){},mixIn:function(properties){for(var propertyName in properties){if(properties.hasOwnProperty(propertyName)){this[propertyName]=properties[propertyName]}}if(properties.hasOwnProperty("toString")){this.toString=properties.toString}},clone:function(){return this.init.prototype.extend(this)}}}());var WordArray=C_lib.WordArray=Base.extend({init:function(words,sigBytes){words=this.words=words||[];if(sigBytes!=undefined){this.sigBytes=sigBytes}else{this.sigBytes=words.length*4}},toString:function(encoder){return(encoder||Hex).stringify(this)},concat:function(wordArray){var thisWords=this.words;var thatWords=wordArray.words;var thisSigBytes=this.sigBytes;var thatSigBytes=wordArray.sigBytes;this.clamp();if(thisSigBytes%4){for(var i=0;i<thatSigBytes;i++){var thatByte=(thatWords[i>>>2]>>>(24-(i%4)*8))&255;thisWords[(thisSigBytes+i)>>>2]|=thatByte<<(24-((thisSigBytes+i)%4)*8)}}else{for(var i=0;i<thatSigBytes;i+=4){thisWords[(thisSigBytes+i)>>>2]=thatWords[i>>>2]}}this.sigBytes+=thatSigBytes;return this},clamp:function(){var words=this.words;var sigBytes=this.sigBytes;words[sigBytes>>>2]&=4294967295<<(32-(sigBytes%4)*8);words.length=Math.ceil(sigBytes/4)},clone:function(){var clone=Base.clone.call(this);clone.words=this.words.slice(0);return clone},random:function(nBytes){var words=[];var r=function(m_w){var m_w=m_w;var m_z=987654321;var mask=4294967295;return function(){m_z=(36969*(m_z&65535)+(m_z>>16))&mask;m_w=(18000*(m_w&65535)+(m_w>>16))&mask;var result=((m_z<<16)+m_w)&mask;result/=4294967296;result+=0.5;return result*(Math.random()>0.5?1:-1)}};for(var i=0,rcache;i<nBytes;i+=4){var _r=r((rcache||Math.random())*4294967296);rcache=_r()*987654071;words.push((_r()*4294967296)|0)}return new WordArray.init(words,nBytes)}});var C_enc=C.enc={};var Hex=C_enc.Hex={stringify:function(wordArray){var words=wordArray.words;var sigBytes=wordArray.sigBytes;var hexChars=[];for(var i=0;i<sigBytes;i++){var bite=(words[i>>>2]>>>(24-(i%4)*8))&255;hexChars.push((bite>>>4).toString(16));hexChars.push((bite&15).toString(16))}return hexChars.join("")},parse:function(hexStr){var hexStrLength=hexStr.length;var words=[];for(var i=0;i<hexStrLength;i+=2){words[i>>>3]|=parseInt(hexStr.substr(i,2),16)<<(24-(i%8)*4)}return new WordArray.init(words,hexStrLength/2)}};var Latin1=C_enc.Latin1={stringify:function(wordArray){var words=wordArray.words;var sigBytes=wordArray.sigBytes;var latin1Chars=[];for(var i=0;i<sigBytes;i++){var bite=(words[i>>>2]>>>(24-(i%4)*8))&255;latin1Chars.push(String.fromCharCode(bite))}return latin1Chars.join("")},parse:function(latin1Str){var latin1StrLength=latin1Str.length;var words=[];for(var i=0;i<latin1StrLength;i++){words[i>>>2]|=(latin1Str.charCodeAt(i)&255)<<(24-(i%4)*8)}return new WordArray.init(words,latin1StrLength)}};var Utf8=C_enc.Utf8={stringify:function(wordArray){try{return decodeURIComponent(escape(Latin1.stringify(wordArray)))}catch(e){throw new Error("Malformed UTF-8 data")}},parse:function(utf8Str){return Latin1.parse(unescape(encodeURIComponent(utf8Str)))}};var BufferedBlockAlgorithm=C_lib.BufferedBlockAlgorithm=Base.extend({reset:function(){this._data=new WordArray.init();this._nDataBytes=0},_append:function(data){if(typeof data=="string"){data=Utf8.parse(data)}this._data.concat(data);this._nDataBytes+=data.sigBytes},_process:function(doFlush){var processedWords;var data=this._data;var dataWords=data.words;var dataSigBytes=data.sigBytes;var blockSize=this.blockSize;var blockSizeBytes=blockSize*4;var nBlocksReady=dataSigBytes/blockSizeBytes;if(doFlush){nBlocksReady=Math.ceil(nBlocksReady)}else{nBlocksReady=Math.max((nBlocksReady|0)-this._minBufferSize,0)}var nWordsReady=nBlocksReady*blockSize;var nBytesReady=Math.min(nWordsReady*4,dataSigBytes);if(nWordsReady){for(var offset=0;offset<nWordsReady;offset+=blockSize){this._doProcessBlock(dataWords,offset)}processedWords=dataWords.splice(0,nWordsReady);data.sigBytes-=nBytesReady}return new WordArray.init(processedWords,nBytesReady)},clone:function(){var clone=Base.clone.call(this);clone._data=this._data.clone();return clone},_minBufferSize:0});var Hasher=C_lib.Hasher=BufferedBlockAlgorithm.extend({cfg:Base.extend(),init:function(cfg){this.cfg=this.cfg.extend(cfg);this.reset()},reset:function(){BufferedBlockAlgorithm.reset.call(this);this._doReset()},update:function(messageUpdate){this._append(messageUpdate);
        this._process();return this},finalize:function(messageUpdate){if(messageUpdate){this._append(messageUpdate)}var hash=this._doFinalize();return hash},blockSize:512/32,_createHelper:function(hasher){return function(message,cfg){return new hasher.init(cfg).finalize(message)}},_createHmacHelper:function(hasher){return function(message,key){return new C_algo.HMAC.init(hasher,key).finalize(message)}}});var C_algo=C.algo={};return C}(Math));(function(){var C=CryptoJS;var C_lib=C.lib;var WordArray=C_lib.WordArray;var C_enc=C.enc;var Base64=C_enc.Base64={stringify:function(wordArray){var words=wordArray.words;var sigBytes=wordArray.sigBytes;var map=this._map;wordArray.clamp();var base64Chars=[];for(var i=0;i<sigBytes;i+=3){var byte1=(words[i>>>2]>>>(24-(i%4)*8))&255;var byte2=(words[(i+1)>>>2]>>>(24-((i+1)%4)*8))&255;var byte3=(words[(i+2)>>>2]>>>(24-((i+2)%4)*8))&255;var triplet=(byte1<<16)|(byte2<<8)|byte3;for(var j=0;(j<4)&&(i+j*0.75<sigBytes);j++){base64Chars.push(map.charAt((triplet>>>(6*(3-j)))&63))}}var paddingChar=map.charAt(64);if(paddingChar){while(base64Chars.length%4){base64Chars.push(paddingChar)}}return base64Chars.join("")},parse:function(base64Str){var base64StrLength=base64Str.length;var map=this._map;var reverseMap=this._reverseMap;if(!reverseMap){reverseMap=this._reverseMap=[];for(var j=0;j<map.length;j++){reverseMap[map.charCodeAt(j)]=j}}var paddingChar=map.charAt(64);if(paddingChar){var paddingIndex=base64Str.indexOf(paddingChar);if(paddingIndex!==-1){base64StrLength=paddingIndex}}return parseLoop(base64Str,base64StrLength,reverseMap)},_map:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/="};function parseLoop(base64Str,base64StrLength,reverseMap){var words=[];var nBytes=0;for(var i=0;i<base64StrLength;i++){if(i%4){var bits1=reverseMap[base64Str.charCodeAt(i-1)]<<((i%4)*2);var bits2=reverseMap[base64Str.charCodeAt(i)]>>>(6-(i%4)*2);var bitsCombined=bits1|bits2;words[nBytes>>>2]|=bitsCombined<<(24-(nBytes%4)*8);nBytes++}}return WordArray.create(words,nBytes)}}());CryptoJS.lib.Cipher||(function(undefined){var C=CryptoJS;var C_lib=C.lib;var Base=C_lib.Base;var WordArray=C_lib.WordArray;var BufferedBlockAlgorithm=C_lib.BufferedBlockAlgorithm;var C_enc=C.enc;var Utf8=C_enc.Utf8;var Base64=C_enc.Base64;var C_algo=C.algo;var EvpKDF=C_algo.EvpKDF;var Cipher=C_lib.Cipher=BufferedBlockAlgorithm.extend({cfg:Base.extend(),createEncryptor:function(key,cfg){return this.create(this._ENC_XFORM_MODE,key,cfg)},createDecryptor:function(key,cfg){return this.create(this._DEC_XFORM_MODE,key,cfg)},init:function(xformMode,key,cfg){this.cfg=this.cfg.extend(cfg);this._xformMode=xformMode;this._key=key;this.reset()},reset:function(){BufferedBlockAlgorithm.reset.call(this);this._doReset()},process:function(dataUpdate){this._append(dataUpdate);return this._process()},finalize:function(dataUpdate){if(dataUpdate){this._append(dataUpdate)}var finalProcessedData=this._doFinalize();return finalProcessedData},keySize:128/32,ivSize:128/32,_ENC_XFORM_MODE:1,_DEC_XFORM_MODE:2,_createHelper:(function(){function selectCipherStrategy(key){if(typeof key=="string"){return PasswordBasedCipher}else{return SerializableCipher}}return function(cipher){return{encrypt:function(message,key,cfg){return selectCipherStrategy(key).encrypt(cipher,message,key,cfg)},decrypt:function(ciphertext,key,cfg){return selectCipherStrategy(key).decrypt(cipher,ciphertext,key,cfg)}}}}())});var StreamCipher=C_lib.StreamCipher=Cipher.extend({_doFinalize:function(){var finalProcessedBlocks=this._process(!!"flush");return finalProcessedBlocks},blockSize:1});var C_mode=C.mode={};var BlockCipherMode=C_lib.BlockCipherMode=Base.extend({createEncryptor:function(cipher,iv){return this.Encryptor.create(cipher,iv)},createDecryptor:function(cipher,iv){return this.Decryptor.create(cipher,iv)},init:function(cipher,iv){this._cipher=cipher;this._iv=iv}});var CBC=C_mode.CBC=(function(){var CBC=BlockCipherMode.extend();CBC.Encryptor=CBC.extend({processBlock:function(words,offset){var cipher=this._cipher;var blockSize=cipher.blockSize;xorBlock.call(this,words,offset,blockSize);cipher.encryptBlock(words,offset);this._prevBlock=words.slice(offset,offset+blockSize)}});CBC.Decryptor=CBC.extend({processBlock:function(words,offset){var cipher=this._cipher;var blockSize=cipher.blockSize;var thisBlock=words.slice(offset,offset+blockSize);cipher.decryptBlock(words,offset);xorBlock.call(this,words,offset,blockSize);this._prevBlock=thisBlock}});function xorBlock(words,offset,blockSize){var block;var iv=this._iv;if(iv){block=iv;this._iv=undefined}else{block=this._prevBlock}for(var i=0;i<blockSize;i++){words[offset+i]^=block[i]}}return CBC}());var C_pad=C.pad={};var Pkcs7=C_pad.Pkcs7={pad:function(data,blockSize){var blockSizeBytes=blockSize*4;var nPaddingBytes=blockSizeBytes-data.sigBytes%blockSizeBytes;var paddingWord=(nPaddingBytes<<24)|(nPaddingBytes<<16)|(nPaddingBytes<<8)|nPaddingBytes;var paddingWords=[];for(var i=0;i<nPaddingBytes;i+=4){paddingWords.push(paddingWord)}var padding=WordArray.create(paddingWords,nPaddingBytes);
        data.concat(padding)},unpad:function(data){var nPaddingBytes=data.words[(data.sigBytes-1)>>>2]&255;data.sigBytes-=nPaddingBytes}};var BlockCipher=C_lib.BlockCipher=Cipher.extend({cfg:Cipher.cfg.extend({mode:CBC,padding:Pkcs7}),reset:function(){var modeCreator;Cipher.reset.call(this);var cfg=this.cfg;var iv=cfg.iv;var mode=cfg.mode;if(this._xformMode==this._ENC_XFORM_MODE){modeCreator=mode.createEncryptor}else{modeCreator=mode.createDecryptor;this._minBufferSize=1}if(this._mode&&this._mode.__creator==modeCreator){this._mode.init(this,iv&&iv.words)}else{this._mode=modeCreator.call(mode,this,iv&&iv.words);this._mode.__creator=modeCreator}},_doProcessBlock:function(words,offset){this._mode.processBlock(words,offset)},_doFinalize:function(){var finalProcessedBlocks;var padding=this.cfg.padding;if(this._xformMode==this._ENC_XFORM_MODE){padding.pad(this._data,this.blockSize);finalProcessedBlocks=this._process(!!"flush")}else{finalProcessedBlocks=this._process(!!"flush");padding.unpad(finalProcessedBlocks)}return finalProcessedBlocks},blockSize:128/32});var CipherParams=C_lib.CipherParams=Base.extend({init:function(cipherParams){this.mixIn(cipherParams)},toString:function(formatter){return(formatter||this.formatter).stringify(this)}});var C_format=C.format={};var OpenSSLFormatter=C_format.OpenSSL={stringify:function(cipherParams){var wordArray;var ciphertext=cipherParams.ciphertext;var salt=cipherParams.salt;if(salt){wordArray=WordArray.create([1398893684,1701076831]).concat(salt).concat(ciphertext)}else{wordArray=ciphertext}return wordArray.toString(Base64)},parse:function(openSSLStr){var salt;var ciphertext=Base64.parse(openSSLStr);var ciphertextWords=ciphertext.words;if(ciphertextWords[0]==1398893684&&ciphertextWords[1]==1701076831){salt=WordArray.create(ciphertextWords.slice(2,4));ciphertextWords.splice(0,4);ciphertext.sigBytes-=16}return CipherParams.create({ciphertext:ciphertext,salt:salt})}};var SerializableCipher=C_lib.SerializableCipher=Base.extend({cfg:Base.extend({format:OpenSSLFormatter}),encrypt:function(cipher,message,key,cfg){cfg=this.cfg.extend(cfg);var encryptor=cipher.createEncryptor(key,cfg);var ciphertext=encryptor.finalize(message);var cipherCfg=encryptor.cfg;return CipherParams.create({ciphertext:ciphertext,key:key,iv:cipherCfg.iv,algorithm:cipher,mode:cipherCfg.mode,padding:cipherCfg.padding,blockSize:cipher.blockSize,formatter:cfg.format})},decrypt:function(cipher,ciphertext,key,cfg){cfg=this.cfg.extend(cfg);ciphertext=this._parse(ciphertext,cfg.format);var plaintext=cipher.createDecryptor(key,cfg).finalize(ciphertext.ciphertext);return plaintext},_parse:function(ciphertext,format){if(typeof ciphertext=="string"){return format.parse(ciphertext,this)}else{return ciphertext}}});var C_kdf=C.kdf={};var OpenSSLKdf=C_kdf.OpenSSL={execute:function(password,keySize,ivSize,salt){if(!salt){salt=WordArray.random(64/8)}var key=EvpKDF.create({keySize:keySize+ivSize}).compute(password,salt);var iv=WordArray.create(key.words.slice(keySize),ivSize*4);key.sigBytes=keySize*4;return CipherParams.create({key:key,iv:iv,salt:salt})}};var PasswordBasedCipher=C_lib.PasswordBasedCipher=SerializableCipher.extend({cfg:SerializableCipher.cfg.extend({kdf:OpenSSLKdf}),encrypt:function(cipher,message,password,cfg){cfg=this.cfg.extend(cfg);var derivedParams=cfg.kdf.execute(password,cipher.keySize,cipher.ivSize);cfg.iv=derivedParams.iv;var ciphertext=SerializableCipher.encrypt.call(this,cipher,message,derivedParams.key,cfg);ciphertext.mixIn(derivedParams);return ciphertext},decrypt:function(cipher,ciphertext,password,cfg){cfg=this.cfg.extend(cfg);ciphertext=this._parse(ciphertext,cfg.format);var derivedParams=cfg.kdf.execute(password,cipher.keySize,cipher.ivSize,ciphertext.salt);cfg.iv=derivedParams.iv;var plaintext=SerializableCipher.decrypt.call(this,cipher,ciphertext,derivedParams.key,cfg);return plaintext}})}());(function(){var C=CryptoJS;var C_lib=C.lib;var BlockCipher=C_lib.BlockCipher;var C_algo=C.algo;var SBOX=[];var INV_SBOX=[];var SUB_MIX_0=[];var SUB_MIX_1=[];var SUB_MIX_2=[];var SUB_MIX_3=[];var INV_SUB_MIX_0=[];var INV_SUB_MIX_1=[];var INV_SUB_MIX_2=[];var INV_SUB_MIX_3=[];(function(){var d=[];for(var i=0;i<256;i++){if(i<128){d[i]=i<<1}else{d[i]=(i<<1)^283}}var x=0;var xi=0;for(var i=0;i<256;i++){var sx=xi^(xi<<1)^(xi<<2)^(xi<<3)^(xi<<4);sx=(sx>>>8)^(sx&255)^99;SBOX[x]=sx;INV_SBOX[sx]=x;var x2=d[x];var x4=d[x2];var x8=d[x4];var t=(d[sx]*257)^(sx*16843008);SUB_MIX_0[x]=(t<<24)|(t>>>8);SUB_MIX_1[x]=(t<<16)|(t>>>16);SUB_MIX_2[x]=(t<<8)|(t>>>24);SUB_MIX_3[x]=t;var t=(x8*16843009)^(x4*65537)^(x2*257)^(x*16843008);INV_SUB_MIX_0[sx]=(t<<24)|(t>>>8);INV_SUB_MIX_1[sx]=(t<<16)|(t>>>16);INV_SUB_MIX_2[sx]=(t<<8)|(t>>>24);INV_SUB_MIX_3[sx]=t;if(!x){x=xi=1}else{x=x2^d[d[d[x8^x2]]];xi^=d[d[xi]]}}}());var RCON=[0,1,2,4,8,16,32,64,128,27,54];var AES=C_algo.AES=BlockCipher.extend({_doReset:function(){var t;if(this._nRounds&&this._keyPriorReset===this._key){return}var key=this._keyPriorReset=this._key;var keyWords=key.words;var keySize=key.sigBytes/4;
        var nRounds=this._nRounds=keySize+6;var ksRows=(nRounds+1)*4;var keySchedule=this._keySchedule=[];for(var ksRow=0;ksRow<ksRows;ksRow++){if(ksRow<keySize){keySchedule[ksRow]=keyWords[ksRow]}else{t=keySchedule[ksRow-1];if(!(ksRow%keySize)){t=(t<<8)|(t>>>24);t=(SBOX[t>>>24]<<24)|(SBOX[(t>>>16)&255]<<16)|(SBOX[(t>>>8)&255]<<8)|SBOX[t&255];t^=RCON[(ksRow/keySize)|0]<<24}else{if(keySize>6&&ksRow%keySize==4){t=(SBOX[t>>>24]<<24)|(SBOX[(t>>>16)&255]<<16)|(SBOX[(t>>>8)&255]<<8)|SBOX[t&255]}}keySchedule[ksRow]=keySchedule[ksRow-keySize]^t}}var invKeySchedule=this._invKeySchedule=[];for(var invKsRow=0;invKsRow<ksRows;invKsRow++){var ksRow=ksRows-invKsRow;if(invKsRow%4){var t=keySchedule[ksRow]}else{var t=keySchedule[ksRow-4]}if(invKsRow<4||ksRow<=4){invKeySchedule[invKsRow]=t}else{invKeySchedule[invKsRow]=INV_SUB_MIX_0[SBOX[t>>>24]]^INV_SUB_MIX_1[SBOX[(t>>>16)&255]]^INV_SUB_MIX_2[SBOX[(t>>>8)&255]]^INV_SUB_MIX_3[SBOX[t&255]]}}},encryptBlock:function(M,offset){this._doCryptBlock(M,offset,this._keySchedule,SUB_MIX_0,SUB_MIX_1,SUB_MIX_2,SUB_MIX_3,SBOX)},decryptBlock:function(M,offset){var t=M[offset+1];M[offset+1]=M[offset+3];M[offset+3]=t;this._doCryptBlock(M,offset,this._invKeySchedule,INV_SUB_MIX_0,INV_SUB_MIX_1,INV_SUB_MIX_2,INV_SUB_MIX_3,INV_SBOX);var t=M[offset+1];M[offset+1]=M[offset+3];M[offset+3]=t},_doCryptBlock:function(M,offset,keySchedule,SUB_MIX_0,SUB_MIX_1,SUB_MIX_2,SUB_MIX_3,SBOX){var nRounds=this._nRounds;var s0=M[offset]^keySchedule[0];var s1=M[offset+1]^keySchedule[1];var s2=M[offset+2]^keySchedule[2];var s3=M[offset+3]^keySchedule[3];var ksRow=4;for(var round=1;round<nRounds;round++){var t0=SUB_MIX_0[s0>>>24]^SUB_MIX_1[(s1>>>16)&255]^SUB_MIX_2[(s2>>>8)&255]^SUB_MIX_3[s3&255]^keySchedule[ksRow++];var t1=SUB_MIX_0[s1>>>24]^SUB_MIX_1[(s2>>>16)&255]^SUB_MIX_2[(s3>>>8)&255]^SUB_MIX_3[s0&255]^keySchedule[ksRow++];var t2=SUB_MIX_0[s2>>>24]^SUB_MIX_1[(s3>>>16)&255]^SUB_MIX_2[(s0>>>8)&255]^SUB_MIX_3[s1&255]^keySchedule[ksRow++];var t3=SUB_MIX_0[s3>>>24]^SUB_MIX_1[(s0>>>16)&255]^SUB_MIX_2[(s1>>>8)&255]^SUB_MIX_3[s2&255]^keySchedule[ksRow++];s0=t0;s1=t1;s2=t2;s3=t3}var t0=((SBOX[s0>>>24]<<24)|(SBOX[(s1>>>16)&255]<<16)|(SBOX[(s2>>>8)&255]<<8)|SBOX[s3&255])^keySchedule[ksRow++];var t1=((SBOX[s1>>>24]<<24)|(SBOX[(s2>>>16)&255]<<16)|(SBOX[(s3>>>8)&255]<<8)|SBOX[s0&255])^keySchedule[ksRow++];var t2=((SBOX[s2>>>24]<<24)|(SBOX[(s3>>>16)&255]<<16)|(SBOX[(s0>>>8)&255]<<8)|SBOX[s1&255])^keySchedule[ksRow++];var t3=((SBOX[s3>>>24]<<24)|(SBOX[(s0>>>16)&255]<<16)|(SBOX[(s1>>>8)&255]<<8)|SBOX[s2&255])^keySchedule[ksRow++];M[offset]=t0;M[offset+1]=t1;M[offset+2]=t2;M[offset+3]=t3},keySize:256/32});C.AES=BlockCipher._createHelper(AES)}());CryptoJS.mode.CFB=(function(){var CFB=CryptoJS.lib.BlockCipherMode.extend();CFB.Encryptor=CFB.extend({processBlock:function(words,offset){var cipher=this._cipher;var blockSize=cipher.blockSize;generateKeystreamAndEncrypt.call(this,words,offset,blockSize,cipher);this._prevBlock=words.slice(offset,offset+blockSize)}});CFB.Decryptor=CFB.extend({processBlock:function(words,offset){var cipher=this._cipher;var blockSize=cipher.blockSize;var thisBlock=words.slice(offset,offset+blockSize);generateKeystreamAndEncrypt.call(this,words,offset,blockSize,cipher);this._prevBlock=thisBlock}});function generateKeystreamAndEncrypt(words,offset,blockSize,cipher){var keystream;var iv=this._iv;if(iv){keystream=iv.slice(0);this._iv=undefined}else{keystream=this._prevBlock}cipher.encryptBlock(keystream,0);for(var i=0;i<blockSize;i++){words[offset+i]^=keystream[i]}}return CFB}());CryptoJS.mode.ECB=(function(){var ECB=CryptoJS.lib.BlockCipherMode.extend();ECB.Encryptor=ECB.extend({processBlock:function(words,offset){this._cipher.encryptBlock(words,offset)}});ECB.Decryptor=ECB.extend({processBlock:function(words,offset){this._cipher.decryptBlock(words,offset)}});return ECB}());(function(){var C=CryptoJS;var C_lib=C.lib;var WordArray=C_lib.WordArray;var BlockCipher=C_lib.BlockCipher;var C_algo=C.algo;var PC1=[57,49,41,33,25,17,9,1,58,50,42,34,26,18,10,2,59,51,43,35,27,19,11,3,60,52,44,36,63,55,47,39,31,23,15,7,62,54,46,38,30,22,14,6,61,53,45,37,29,21,13,5,28,20,12,4];var PC2=[14,17,11,24,1,5,3,28,15,6,21,10,23,19,12,4,26,8,16,7,27,20,13,2,41,52,31,37,47,55,30,40,51,45,33,48,44,49,39,56,34,53,46,42,50,36,29,32];var BIT_SHIFTS=[1,2,4,6,8,10,12,14,15,17,19,21,23,25,27,28];var SBOX_P=[{0:8421888,268435456:32768,536870912:8421378,805306368:2,1073741824:512,1342177280:8421890,1610612736:8389122,1879048192:8388608,2147483648:514,2415919104:8389120,2684354560:33280,2952790016:8421376,3221225472:32770,3489660928:8388610,3758096384:0,4026531840:33282,134217728:0,402653184:8421890,671088640:33282,939524096:32768,1207959552:8421888,1476395008:512,1744830464:8421378,2013265920:2,2281701376:8389120,2550136832:33280,2818572288:8421376,3087007744:8389122,3355443200:8388610,3623878656:32770,3892314112:514,4160749568:8388608,1:32768,268435457:2,536870913:8421888,805306369:8388608,1073741825:8421378,1342177281:33280,1610612737:512,1879048193:8389122,2147483649:8421890,2415919105:8421376,2684354561:8388610,2952790017:33282,3221225473:514,3489660929:8389120,3758096385:32770,4026531841:0,134217729:8421890,402653185:8421376,671088641:8388608,939524097:512,1207959553:32768,1476395009:8388610,1744830465:2,2013265921:33282,2281701377:32770,2550136833:8389122,2818572289:514,3087007745:8421888,3355443201:8389120,3623878657:0,3892314113:33280,4160749569:8421378},{0:1074282512,16777216:16384,33554432:524288,50331648:1074266128,67108864:1073741840,83886080:1074282496,100663296:1073758208,117440512:16,134217728:540672,150994944:1073758224,167772160:1073741824,184549376:540688,201326592:524304,218103808:0,234881024:16400,251658240:1074266112,8388608:1073758208,25165824:540688,41943040:16,58720256:1073758224,75497472:1074282512,92274688:1073741824,109051904:524288,125829120:1074266128,142606336:524304,159383552:0,176160768:16384,192937984:1074266112,209715200:1073741840,226492416:540672,243269632:1074282496,260046848:16400,268435456:0,285212672:1074266128,301989888:1073758224,318767104:1074282496,335544320:1074266112,352321536:16,369098752:540688,385875968:16384,402653184:16400,419430400:524288,436207616:524304,452984832:1073741840,469762048:540672,486539264:1073758208,503316480:1073741824,520093696:1074282512,276824064:540688,293601280:524288,310378496:1074266112,327155712:16384,343932928:1073758208,360710144:1074282512,377487360:16,394264576:1073741824,411041792:1074282496,427819008:1073741840,444596224:1073758224,461373440:524304,478150656:0,494927872:16400,511705088:1074266128,528482304:540672},{0:260,1048576:0,2097152:67109120,3145728:65796,4194304:65540,5242880:67108868,6291456:67174660,7340032:67174400,8388608:67108864,9437184:67174656,10485760:65792,11534336:67174404,12582912:67109124,13631488:65536,14680064:4,15728640:256,524288:67174656,1572864:67174404,2621440:0,3670016:67109120,4718592:67108868,5767168:65536,6815744:65540,7864320:260,8912896:4,9961472:256,11010048:67174400,12058624:65796,13107200:65792,14155776:67109124,15204352:67174660,16252928:67108864,16777216:67174656,17825792:65540,18874368:65536,19922944:67109120,20971520:256,22020096:67174660,23068672:67108868,24117248:0,25165824:67109124,26214400:67108864,27262976:4,28311552:65792,29360128:67174400,30408704:260,31457280:65796,32505856:67174404,17301504:67108864,18350080:260,19398656:67174656,20447232:0,21495808:65540,22544384:67109120,23592960:256,24641536:67174404,25690112:65536,26738688:67174660,27787264:65796,28835840:67108868,29884416:67109124,30932992:67174400,31981568:4,33030144:65792},{0:2151682048,65536:2147487808,131072:4198464,196608:2151677952,262144:0,327680:4198400,393216:2147483712,458752:4194368,524288:2147483648,589824:4194304,655360:64,720896:2147487744,786432:2151678016,851968:4160,917504:4096,983040:2151682112,32768:2147487808,98304:64,163840:2151678016,229376:2147487744,294912:4198400,360448:2151682112,425984:0,491520:2151677952,557056:4096,622592:2151682048,688128:4194304,753664:4160,819200:2147483648,884736:4194368,950272:4198464,1015808:2147483712,1048576:4194368,1114112:4198400,1179648:2147483712,1245184:0,1310720:4160,1376256:2151678016,1441792:2151682048,1507328:2147487808,1572864:2151682112,1638400:2147483648,1703936:2151677952,1769472:4198464,1835008:2147487744,1900544:4194304,1966080:64,2031616:4096,1081344:2151677952,1146880:2151682112,1212416:0,1277952:4198400,1343488:4194368,1409024:2147483648,1474560:2147487808,1540096:64,1605632:2147483712,1671168:4096,1736704:2147487744,1802240:2151678016,1867776:4160,1933312:2151682048,1998848:4194304,2064384:4198464},{0:128,4096:17039360,8192:262144,12288:536870912,16384:537133184,20480:16777344,24576:553648256,28672:262272,32768:16777216,36864:537133056,40960:536871040,45056:553910400,49152:553910272,53248:0,57344:17039488,61440:553648128,2048:17039488,6144:553648256,10240:128,14336:17039360,18432:262144,22528:537133184,26624:553910272,30720:536870912,34816:537133056,38912:0,43008:553910400,47104:16777344,51200:536871040,55296:553648128,59392:16777216,63488:262272,65536:262144,69632:128,73728:536870912,77824:553648256,81920:16777344,86016:553910272,90112:537133184,94208:16777216,98304:553910400,102400:553648128,106496:17039360,110592:537133056,114688:262272,118784:536871040,122880:0,126976:17039488,67584:553648256,71680:16777216,75776:17039360,79872:537133184,83968:536870912,88064:17039488,92160:128,96256:553910272,100352:262272,104448:553910400,108544:0,112640:553648128,116736:16777344,120832:262144,124928:537133056,129024:536871040},{0:268435464,256:8192,512:270532608,768:270540808,1024:268443648,1280:2097152,1536:2097160,1792:268435456,2048:0,2304:268443656,2560:2105344,2816:8,3072:270532616,3328:2105352,3584:8200,3840:270540800,128:270532608,384:270540808,640:8,896:2097152,1152:2105352,1408:268435464,1664:268443648,1920:8200,2176:2097160,2432:8192,2688:268443656,2944:270532616,3200:0,3456:270540800,3712:2105344,3968:268435456,4096:268443648,4352:270532616,4608:270540808,4864:8200,5120:2097152,5376:268435456,5632:268435464,5888:2105344,6144:2105352,6400:0,6656:8,6912:270532608,7168:8192,7424:268443656,7680:270540800,7936:2097160,4224:8,4480:2105344,4736:2097152,4992:268435464,5248:268443648,5504:8200,5760:270540808,6016:270532608,6272:270540800,6528:270532616,6784:8192,7040:2105352,7296:2097160,7552:0,7808:268435456,8064:268443656},{0:1048576,16:33555457,32:1024,48:1049601,64:34604033,80:0,96:1,112:34603009,128:33555456,144:1048577,160:33554433,176:34604032,192:34603008,208:1025,224:1049600,240:33554432,8:34603009,24:0,40:33555457,56:34604032,72:1048576,88:33554433,104:33554432,120:1025,136:1049601,152:33555456,168:34603008,184:1048577,200:1024,216:34604033,232:1,248:1049600,256:33554432,272:1048576,288:33555457,304:34603009,320:1048577,336:33555456,352:34604032,368:1049601,384:1025,400:34604033,416:1049600,432:1,448:0,464:34603008,480:33554433,496:1024,264:1049600,280:33555457,296:34603009,312:1,328:33554432,344:1048576,360:1025,376:34604032,392:33554433,408:34603008,424:0,440:34604033,456:1049601,472:1024,488:33555456,504:1048577},{0:134219808,1:131072,2:134217728,3:32,4:131104,5:134350880,6:134350848,7:2048,8:134348800,9:134219776,10:133120,11:134348832,12:2080,13:0,14:134217760,15:133152,2147483648:2048,2147483649:134350880,2147483650:134219808,2147483651:134217728,2147483652:134348800,2147483653:133120,2147483654:133152,2147483655:32,2147483656:134217760,2147483657:2080,2147483658:131104,2147483659:134350848,2147483660:0,2147483661:134348832,2147483662:134219776,2147483663:131072,16:133152,17:134350848,18:32,19:2048,20:134219776,21:134217760,22:134348832,23:131072,24:0,25:131104,26:134348800,27:134219808,28:134350880,29:133120,30:2080,31:134217728,2147483664:131072,2147483665:2048,2147483666:134348832,2147483667:133152,2147483668:32,2147483669:134348800,2147483670:134217728,2147483671:134219808,2147483672:134350880,2147483673:134217760,2147483674:134219776,2147483675:0,2147483676:133120,2147483677:2080,2147483678:131104,2147483679:134350848}];
    var SBOX_MASK=[4160749569,528482304,33030144,2064384,129024,8064,504,2147483679];var DES=C_algo.DES=BlockCipher.extend({_doReset:function(){var key=this._key;var keyWords=key.words;var keyBits=[];for(var i=0;i<56;i++){var keyBitPos=PC1[i]-1;keyBits[i]=(keyWords[keyBitPos>>>5]>>>(31-keyBitPos%32))&1}var subKeys=this._subKeys=[];for(var nSubKey=0;nSubKey<16;nSubKey++){var subKey=subKeys[nSubKey]=[];var bitShift=BIT_SHIFTS[nSubKey];for(var i=0;i<24;i++){subKey[(i/6)|0]|=keyBits[((PC2[i]-1)+bitShift)%28]<<(31-i%6);subKey[4+((i/6)|0)]|=keyBits[28+(((PC2[i+24]-1)+bitShift)%28)]<<(31-i%6)}subKey[0]=(subKey[0]<<1)|(subKey[0]>>>31);for(var i=1;i<7;i++){subKey[i]=subKey[i]>>>((i-1)*4+3)}subKey[7]=(subKey[7]<<5)|(subKey[7]>>>27)}var invSubKeys=this._invSubKeys=[];for(var i=0;i<16;i++){invSubKeys[i]=subKeys[15-i]}},encryptBlock:function(M,offset){this._doCryptBlock(M,offset,this._subKeys)},decryptBlock:function(M,offset){this._doCryptBlock(M,offset,this._invSubKeys)},_doCryptBlock:function(M,offset,subKeys){this._lBlock=M[offset];this._rBlock=M[offset+1];exchangeLR.call(this,4,252645135);exchangeLR.call(this,16,65535);exchangeRL.call(this,2,858993459);exchangeRL.call(this,8,16711935);exchangeLR.call(this,1,1431655765);for(var round=0;round<16;round++){var subKey=subKeys[round];var lBlock=this._lBlock;var rBlock=this._rBlock;var f=0;for(var i=0;i<8;i++){f|=SBOX_P[i][((rBlock^subKey[i])&SBOX_MASK[i])>>>0]}this._lBlock=rBlock;this._rBlock=lBlock^f}var t=this._lBlock;this._lBlock=this._rBlock;this._rBlock=t;exchangeLR.call(this,1,1431655765);exchangeRL.call(this,8,16711935);exchangeRL.call(this,2,858993459);exchangeLR.call(this,16,65535);exchangeLR.call(this,4,252645135);M[offset]=this._lBlock;M[offset+1]=this._rBlock},keySize:64/32,ivSize:64/32,blockSize:64/32});function exchangeLR(offset,mask){var t=((this._lBlock>>>offset)^this._rBlock)&mask;this._rBlock^=t;this._lBlock^=t<<offset}function exchangeRL(offset,mask){var t=((this._rBlock>>>offset)^this._lBlock)&mask;this._lBlock^=t;this._rBlock^=t<<offset}C.DES=BlockCipher._createHelper(DES);var TripleDES=C_algo.TripleDES=BlockCipher.extend({_doReset:function(){var key=this._key;var keyWords=key.words;this._des1=DES.createEncryptor(WordArray.create(keyWords.slice(0,2)));this._des2=DES.createEncryptor(WordArray.create(keyWords.slice(2,4)));this._des3=DES.createEncryptor(WordArray.create(keyWords.slice(4,6)))},encryptBlock:function(M,offset){this._des1.encryptBlock(M,offset);this._des2.decryptBlock(M,offset);this._des3.encryptBlock(M,offset)},decryptBlock:function(M,offset){this._des3.decryptBlock(M,offset);this._des2.encryptBlock(M,offset);this._des1.decryptBlock(M,offset)},keySize:192/32,ivSize:64/32,blockSize:64/32});C.TripleDES=BlockCipher._createHelper(TripleDES)}());CryptoJS.pad.ZeroPadding={pad:function(data,blockSize){var blockSizeBytes=blockSize*4;data.clamp();data.sigBytes+=blockSizeBytes-((data.sigBytes%blockSizeBytes)||blockSizeBytes)},unpad:function(data){var dataWords=data.words;var i=data.sigBytes-1;for(var i=data.sigBytes-1;i>=0;i--){if(((dataWords[i>>>2]>>>(24-(i%4)*8))&255)){data.sigBytes=i+1;break}}}};

//判断浏览器是pc还是移动端
var isPc = function () {
    var userAgentInfo = navigator.userAgent;
    var Agents = new Array("Android", "iPhone", "SymbianOS", "Windows Phone", "iPad", "iPod");
    var flag = true;
    for (var v = 0; v < Agents.length; v++) {
        if (userAgentInfo.indexOf(Agents[v]) > 0) {
            flag = false;
            break;
        }
    }
    return flag;
};

//格式化全站的 ajax post
function rePost(url, postData, callBack) {
    if(!url) return;
    $.post(url, postData, callBack, 'json');
}
//js trim去左右字符(空格) 去4次左右两边符号
function trim(str, node) {
    node = node || " ";
    var len = node.length;
    if (str.substr(0, len) == node) str = str.substr(len);
    if (str.substr(0, len) == node) str = str.substr(len);
    if (str.substr(0, len) == node) str = str.substr(len);
    if (str.substr(0, len) == node) str = str.substr(len);
    if (str.substr(str.length - len, len) == node) str = str.substr(0, str.length - len);
    if (str.substr(str.length - len, len) == node) str = str.substr(0, str.length - len);
    if (str.substr(str.length - len, len) == node) str = str.substr(0, str.length - len);
    if (str.substr(str.length - len, len) == node) str = str.substr(0, str.length - len);
    return str;
}
//缩写原生的判断对象是否存在
function isUndefined(variable) {return typeof variable == 'undefined' ? true : false;}

//返回顶部
function backTop() {
    $('body,html').stop().animate({scrollTop:0}, 500);
}
//重复字符串
function repeat(html, n) {
    return (new Array(n + 1)).join(html);
}
//obj转json字符串
function objToJson(obj) {
    try{
        var seen = [];
        var json = JSON.stringify(obj, function(key, val) {
            if (typeof val == "object") {
                if (seen.indexOf(val) >= 0) return;
                seen.push(val)
            }
            return val;
        });
        return json;
    }catch(e){
        return e;
    }
}
//不能按esc就关闭，因为窗口里可能有视频播放，esc是退出全屏专用，而且窗口右边有大肚脐关闭按钮了，故去掉此快捷键。
//四舍五入 保留2位小数
function formatFloat(src, pos) {
    pos = isUndefined(pos) ? 2 : pos;
    src = Math.round(src*Math.pow(10, pos))/Math.pow(10, pos);//先四舍五入
    //补齐后面的0
    src=Math.round(parseFloat(src)*100)/100;
    var xsd= src.toString().split(".");
    if(xsd.length==1){
        src=src.toString();
        return src;
    }
    if(xsd.length>1){
        if(xsd[1].length<2){
            src=src.toString();
        }
        return src;
    }
}
//制保留2位小数，如：2，会在2后面补上00.即2.00
function toDecimal2(x, len) {
    len = isUndefined(len) ? 2 : len;
    var f = parseFloat(x);
    if (isNaN(f)) {
        return false;
    }
    var f = Math.round(x*100)/100;
    var s = f.toString();
    var rs = s.indexOf('.');
    if (rs < 0) {
        rs = s.length;
        s += '.';
    }
    while (s.length <= rs + len) {
        s += '0';
    }
    return s;
}
// 两个浮点数求和 保留2位小数
function bcadd(num1,num2, len) {
    len = len || 0;
    var r1,r2,m;
    try{
        r1 = num1.toString().split('.')[1].length;
    }catch(e){
        r1 = 0;
    }
    try{
        r2=num2.toString().split(".")[1].length;
    }catch(e){
        r2=0;
    }
    m=Math.pow(10,Math.max(r1,r2));
    var result = Math.round(num1*m+num2*m)/m;
    if(len>0) result = toDecimal2(result, len);
    return result;
}

// 两个浮点数相减
function bcsub(num1,num2){
    var r1,r2,m;
    try{
        r1 = num1.toString().split('.')[1].length;
    }catch(e){
        r1 = 0;
    }
    try{
        r2=num2.toString().split(".")[1].length;
    }catch(e){
        r2=0;
    }
    m=Math.pow(10,Math.max(r1,r2));
    var result = Math.round(num1*m-num2*m)/m;
    return formatFloat(result, 2);
}
// 两数相除
function bcdiv(num1,num2){
    var t1,t2,r1,r2;
    try{
        t1 = num1.toString().split('.')[1].length;
    }catch(e){
        t1 = 0;
    }
    try{
        t2=num2.toString().split(".")[1].length;
    }catch(e){
        t2=0;
    }
    r1=Number(num1.toString().replace(".",""));
    r2=Number(num2.toString().replace(".",""));
    var result = (r1/r2)*Math.pow(10,t2-t1);
    return formatFloat(result, 2);
}
//两数相乘
function bcmul(num1,num2){
    var m=0,s1=num1.toString(),s2=num2.toString();
    try{m+=s1.split(".")[1].length}catch(e){};
    try{m+=s2.split(".")[1].length}catch(e){};
    return Number(s1.replace(".",""))*Number(s2.replace(".",""))/Math.pow(10,m);
}

//判断手机格式是否正确
function checkPhoneFormat(phone) {
    phone = phone || '';
    var patrn = /(^0{0,1}1[3|4|5|6|7|8|9][0-9]{9}$)/;
    return patrn.exec(phone);
}
//判断邮箱格式是否正确
function checkEmailFormat(email) {
    email = email || '';
    var email_zhengzhe = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return email_zhengzhe.exec(email);
}
//图片去缓存
function noCacheImg(url) {
    if(!url) return '';
    if(url.indexOf('?') != -1) return url += '&r_='+ formatFloat(Math.random()*100);
    return url += '?r_='+ formatFloat(Math.random()*100);
}
//字符串每隔N个字符插入字符串
function numberSplitByLen(str, num, joinWith) {
    eval("str = str.replace(/"+ joinWith +"/g,'')");
    num = num || 4;
    joinWith = joinWith || ' ';
    var begin = 0,
        end = begin + num;
    var result = [],
        strLength = str.length;

    if (end >= strLength)  return str;
    var ss;
    while (end < strLength) {
        ss = str.slice(begin, end);
        result.push(ss);
        begin = begin + num;
        end = begin + num;
        if (end >= strLength) {
            end = strLength;
            ss = str.slice(begin, end);
            result.push(ss);
            break;
        }
    }
    return result.join(joinWith);
}
//判断对象为空
function objIsNull(obj) {
    return JSON.stringify(obj) == '{}';
}
//保留N位数字字符串 非四舍五入
function numKeepLen(number, pos) {
    pos = pos || 2;
    number = number.toString();
    if (number.indexOf('.') != -1) {
        var numArray = number.split('.');
        return numArray[0] + "." + numArray[1].substr(0, pos);
    } else {
        return number;
    }
}
//获取当前时间
function time() {
    return Date.parse(new Date())/1000;
}
//时间戳转文本
function timeToYMD(date){
    var date = new Date(date*1000);//如果date为10位不需要乘1000
    var Y = date.getFullYear() + '-';
    var M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
    var D = (date.getDate() < 10 ? '0' + (date.getDate()) : date.getDate()) + ' ';
    var h = (date.getHours() < 10 ? '0' + date.getHours() : date.getHours()) + ':';
    var m = (date.getMinutes() <10 ? '0' + date.getMinutes() : date.getMinutes()) + ':';
    var s = (date.getSeconds() <10 ? '0' + date.getSeconds() : date.getSeconds());
    return Y+M+D+h+m+s;
}
//时间戳转文本，并去除秒
function noSecond(albTime) {
    var timeStr = new Date(parseInt(albTime) * 1000).toLocaleString().replace(/[^0-9^\^:^\s/]/g,'').replace(/\//g,'-');
    var timeAy = timeStr.split(':');
    timeAy.splice(-1,1);
    return timeAy.join(':');
}
//模拟php isset
function isset(obj) {
    return typeof(obj) !="undefined";
}
//合并2个对象的同key的值为数组 joinObjVal({a: [1,2,3], b:{c: [6,7,8], d: 'ff'}},{Fa: '334',b: {c:'aaa', d:'gg'}})
//union 是否取唯一
function joinObjVal(obj1, obj2, union) {
    union = union || false;
    if(objIsNull(obj1)) return obj2;
    $.each(obj1, function (key_, val_) {
        if(obj2[key_]) {
            if($.isArray(val_)) {
                if($.isArray(obj2[key_])) {
                    obj1[key_] = val_.concat(obj2[key_]);
                } else {
                    val_[val_.length] = obj2[key_];
                    obj1[key_] = val_;
                }
            } else if(typeof val_ == 'object') {
                obj1[key_] = joinObjVal(val_, obj2[key_])
            } else {
                if($.isArray(obj2[key_])) {
                    obj2[key_][obj2[key_].length] = val_;
                    obj1[key_] = obj2[key_];
                } else {
                    obj1[key_] = [val_, obj2[key_]];
                }
            }
            if(union) obj1[key_] = $.unique(obj1[key_]);
        }
    });
    //补全没有的数组
    $.each(obj2, function (key2_, val2_) {
        if(!obj1[key2_]) {
            obj1[key2_] = val2_;
        }
    });
    return obj1;
}
//检索数组 文本类型：数字和字符都支持 不区分1和'1'
function strInArray(str, array_) {
    var exist_ = -1;
    $.each(array_, function (n, item_) {
        if(item_ == str) {
            exist_ = n;
            return false; //break
        }
    });
    return exist_;
}
//判断是否在数组中
function inArray(val, array_) {
    return $.inArray(val, array_) != -1;
}

//判断是否数组
function isArray(source) {
    return '[object Array]' == Object.prototype.toString.call(source);
}
//打印json
function printJson(sonString) {
    var objs = JSON.parse(sonString);
    var jiexiObj = function (obj_, level, spaceParent) {
        var spac1 = new Array(level).join('&nbsp;');
        var spac2 = spac1 + new Array(7).join('&nbsp;');
        var __str = '',
            frontStr,
            endStr;
        if (typeof obj_[0] != 'undefined') {
            console.log(obj_[0]);
            frontStr = '[';
            endStr = ']';
        } else {
            frontStr = '{';
            endStr = '}'
        }
        __str += spac1 + frontStr + '<br>';
        $.each(obj_, function (index, v) {
            if (isNaN(index)) {
                __str += spac2 + spaceParent + '"' + index + '" => ';
            } else {
                __str += spac2 + spaceParent + '[' + index + '] => ';
            }
            if (typeof v == 'object') {
                __str += jiexiObj(v, (level + 1), spac1 + spac2) + ',<br>';
            } else {
                __str += v + ',<br>';
            }
        });
        __str += spac1 + spac2 + spaceParent + endStr  + '';
        return __str;
    };
    return jiexiObj(objs, 0, '');
}
//字符格式
function isString(val) {
    return typeof val == 'string' || typeof val == 'number';
}
//封装post之后的动作
function postData(options) {
    //[属性：success_value,post_url,post_data,msg,msg_hide,func]
    // success_value //成功时的回调值，'0308'/ ['0043', '0113']
    // post_url //post 接口
    // load_bg //load是否加背景
    // post_data //post 数据包
    // success_func //成功时执行的动作
    //err_func://失败时执行的动作
    options = options || {};
    var successVal = options['success_value'] || '';
    var successKey = options['success_key'] || '';
    successVal = Array.isArray(successVal) ? successVal : [successVal];
    var postUrl = options['post_url'] || options['url'] || '';
    var loadBg = !isUndefined(options['load_bg']) ? options['load_bg'] :  false;
    var postData = options['post_data'] || {};//post data
    var successFunc = options['success_func'] || '';//成功回调
    var errFunc = options['err_func'] || '';//失败回调
    if(loadBg) loading();
    rePost(postUrl, postData,function(data) {
        if(loadBg) noLoading();
        // console.log(successVal);
        // console.log(successKey);
        // console.log(data);
        // console.log(data[successKey]);
        if(!data) {
            console.log('post result: no data');
            return;
        }
        // console.log(data);
        // console.log('successKey:'+ successKey);
        // console.log(data[successKey]);
        // console.log('successVal');
        // console.log(successVal);
        // console.log(strInArray(data[successKey], successVal));
        if(successVal && !isUndefined(data[successKey]) && strInArray(data[successKey], successVal) == -1) {
            // console.log('eeeeeee');
            if(errFunc) errFunc(data);
        } else {
            // console.log('successFunc:'+ successFunc);
            //可能这里会执行关闭所有（最新）窗口，所以要提前执行，防止将默认的提示语误关。
            if(successFunc) {
                if(isString(successFunc)) {
                    eval(successFunc);
                } else {
                    successFunc(data);
                }
            }
        }
    });
};
//获取url参数
function getUrlParam(name, explode, url) {
    var param = window.location.href;
    if (url) {
        if (explode) {
            param = url.substr(url.indexOf(explode) + 1);
        } else {
            param = url.substr(url.indexOf('?') + 1);
        }
    } else {
        if (explode) {
            param = window.location.href.substr(window.location.href.indexOf(explode) + 1);
        }
    }
    var reg = new RegExp("(^|&\?)" + name + "=([^&]*)(&|$)");
    var r = param.match(reg);
    if (r != null) return unescape(r[2]);
    return '';
}

//加密
function aesEncode(data, key) {
    if(typeof key != 'string') key+= '';
    var key = CryptoJS.enc.Utf8.parse(key),
        iv = '0987654321acbdef',
        iv = CryptoJS.enc.Utf8.parse(iv),
        encrypted = CryptoJS.AES.encrypt(data, key,
            {
                iv: iv,
                mode: CryptoJS.mode.CBC,
                padding: CryptoJS.pad.ZeroPadding
            });
    //返回的是base64格式的密文
    return encrypted.toString();
}
//解密
function aesDecode(encrypted, key) {
    if(typeof key != 'string') key+= '';
    var key = CryptoJS.enc.Utf8.parse(key);
    var iv = CryptoJS.enc.Utf8.parse('0987654321acbdef');
    var decrypted = CryptoJS.AES.decrypt(encrypted, key,
        {
            iv: iv,
            mode: CryptoJS.mode.CBC,
            padding: CryptoJS.pad.ZeroPadding
        });
    return decrypted.toString(CryptoJS.enc.Utf8);
}
// 存储数据
function saveStorage(name, val){
    localStorage.setItem(name, JSON.stringify(val));
}
// 获取存储数据
function getStorage(name){
    return JSON.parse(localStorage.getItem(name));
}
//输出可用函数
/*
* $.browser 判断浏览器类型
* obj.DragUtil(boxBarId, obj.position_type); //注册拖拽事件
* $.url.decode; //url解码
* backTop 返回顶部
* repeat重复字符串
* formatFloat 四舍五入 保留2位小数
* numKeepLen 非四舍五入 保留N位小数
* trim
* rePost 格式化post
* isUndefined判断对象是否定义
* isPc 判断PC端 OR WAP端
* bcsub bcadd bcdiv bcmul 加减乘除高精度算法
*
*
* */