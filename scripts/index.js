/**
 * index.js
 */
const HEIGHT = 135;


$(function(){
	//'''''''''''''''''''''''''''''''''''''''''''''''
	// Watsonﾚｽﾎﾟﾝｽ取得 Ajax処理
	//,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
	var getWatsonAjax = function (_q,_id,_node){
		$.ajax({
	        type: 'post',
	        url: './php/conversation.php',
	        data: {
	        	question :_q,
	        	conversation_id :_id,
	        	dialog_node:_node
	        }
	    }).then(function( ret ){
	    	// 処理成功時は解答を設定する
	    	$('#mCSB_1_container').append('<div class="input-val-left clip">&nbsp;</div><div class="input-val-right">' + ret.output.text + '</div>');
	    	// ﾚｽﾎﾟﾝｽの高さをﾘｸｴｽﾄの高さに設定
	    	$('.input-val-left').last().height($('.input-val-right').last().height());
	    	// idをhiddenに設定
	    	$('#conversation_id').val(ret.context.conversation_id);
	    	// ﾉｰﾄﾞをhiddenに設定
	    	$('#dialog_node').val(ret.context.system.dialog_stack[0].dialog_node);
	    	// ｽｸﾛｰﾙを一番下に設定
	    	$(".input-area").mCustomScrollbar("scrollTo","bottom");
	    	// 活性化
			$('#input-question').prop('disabled',false);
	    	// 検索ｶﾃｺﾞﾘが指定されていない場合は商品検索処理を行わない
	    	if(ret.output.search_index){
	    		// 非活性化
				$('#input-question').prop('disabled',true);
		    	// 商品検索処理
	    		getRakutenAjax(ret.output.search_index,ret.output.keywords);
	    	}
	    	// ﾌｫｰｶｽをｾｯﾄ
	    	$('#input-question').focus();
	    },
	    function(e){
	    	// ｴﾗｰ時はｱﾗｰﾄ
	    	alert('error');
	    	$('.input-area').append(e.responseText);
	    	// 活性化
			$('#input-question').prop('disabled',false);
			// ﾌｫｰｶｽをｾｯﾄ
	    	$('#input-question').focus();
	    });
	}
	//'''''''''''''''''''''''''''''''''''''''''''''''
	// Rakutenﾚｽﾎﾟﾝｽ取得 Ajax処理
	//,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
	var getRakutenAjax = function (_search_index,_keywords){
		$.ajax({
	        type: 'post',
	        url: './php/search.php',
	        data: {
	        	search_index :_search_index,
	        	keywords :_keywords
	        }
	    }).then(function( json ){
	    	// 表示対象をﾗﾝﾀﾞﾑで取得
	    	var index = Math.floor( Math.random() * json.Items.Item.length ) ;
	    	// 表示対象の情報取得
    		var $t = json.Items.Item[index];
    		// 表示画像の設定
    		var html = '<a target="_blank" href="' + $t.itemUrl + '">';
    		html += "<img src='" + $t.mediumImageUrls.imageUrl[0] +  "'>";
    		html += "</a>";

    		// 取得した商品情報を表示
	    	$('#mCSB_1_container').append('<div class="input-val-left clip">&nbsp;</div><div class="input-val-right">' + html + '</div>');
	    	// 画像表示用に高さ設定
	        $('.input-val-left').last().height(HEIGHT);
	        $('.input-val-right').last().height(HEIGHT);

	    	// 検索ｶﾃｺﾞﾘをhiddenに設定
	    	$('#search_index').val('');
	    	// ｷｰﾜｰﾄﾞをhiddenに設定
	    	$('#keywords').val('');
	    	// ｽｸﾛｰﾙを一番下に設定
	    	$(".input-area").mCustomScrollbar("scrollTo","bottom");
	    	// 活性化
			$('#input-question').prop('disabled',false);
	    	// ﾌｫｰｶｽをｾｯﾄ
	    	$('#input-question').focus();
	    },
	    function(e){
	    	// ｴﾗｰ時はｱﾗｰﾄ
	    	alert('error');
	    	$('.input-area').append(e.responseText);
	    	// 活性化
			$('#input-question').prop('disabled',false);
			// ﾌｫｰｶｽをｾｯﾄ
	    	$('#input-question').focus();
	    });
	}

	//'''''''''''''''''''''''''''''''''''''''''''''''
	// ﾃｷｽﾄｴﾘｱのｷｰｲﾍﾞﾝﾄ設定
	//,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
	$( '#input-question' ).keypress( function ( e ) {
		// ｴﾝﾀｰｷｰ押下時に入力内容を表示する
		if ( e.which == 13 ) {
			var $t = $(this);
			// 文字入力がされている場合のみ処理を行う
			if ($t.val() !== '')
			{
				// 非活性化
				$('#input-question').prop('disabled',true);
				// ﾁｬｯﾄｴﾘｱに入力した内容を表示する
				$('#mCSB_1_container').append('<div class="input-val-left">' + $t.val() + '</div><div class="input-val-right clip">&nbsp;</div>');
				// ﾘｸｴｽﾄの高さをﾚｽﾎﾟﾝｽの高さに設定
		    	$('.input-val-right').last().height($('.input-val-left').last().height());
				// ajaxで入力した質問に対しての解答を取得する
		    	getWatsonAjax($t.val(),$('#conversation_id').val(),$('#dialog_node').val());
				// 入力内容をｸﾘｱ
				$t.val('');
			}
			// ﾊﾞﾌﾞﾘﾝｸﾞを止める
			return false;
		}
	} );
	//'''''''''''''''''''''''''''''''''''''''''''''''
	// 初回表示処理
	//,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
	// 初回表示用のAjaxﾘｸｴｽﾄ
	getWatsonAjax('','','');
	// ｽｸﾛｰﾙのﾃｰﾏ
	$(".input-area").mCustomScrollbar({
	    theme:"dark",
	    advanced:{
	    	updateOnContentResize: true
        }
	});
	// ﾌｫｰｶｽをｾｯﾄ
	$('#input-question').focus();
})
