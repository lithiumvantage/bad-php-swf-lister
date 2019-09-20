<?php
	function truncateStr($str, $len=5) {
		if(!$str) return null;
		if(!$len||intval($len)<5) $len=5;
		$buf="";
		for($i2=0;$i2<$len;$i2++) {
			$buf .= $str[$i2];
		}
		return $buf;
	}
	function FileSizeConvert($bytes) {
		$bytes = floatval($bytes);
		$arBytes = array(
			0 => array(
				"UNIT" => "TB",
				"VALUE" => pow(1024, 4)
			),
			1 => array(
				"UNIT" => "GB",
				"VALUE" => pow(1024, 3)
			),
			2 => array(
				"UNIT" => "MB",
				"VALUE" => pow(1024, 2)
			),
			3 => array(
				"UNIT" => "KB",
				"VALUE" => 1024
			),
			4 => array(
				"UNIT" => "B",
				"VALUE" => 1
			),
		);
		foreach($arBytes as $arItem) {
			if(($bytes >= $arItem["VALUE"])) {
				$result = str_replace(",", "." , strval(round(($bytes / $arItem["VALUE"]), 2)))." ".$arItem["UNIT"];
				break;
			}
		}
		return $result;
	}
	function cleanFilename($inp) {
		if(!$inp) return "";
		return preg_replace('/[^a-zA-Z0-9\-_.,();\[\] ]/', '', $inp);
	}
	header("Content-type: text/html; charset=UTF-8");
	header("Cache-control: private");
?>
<!DOCTYPE html>
<html>
	<head>
		<title>swf thing</title>
		<style type="text/css">
			body {
				width: 100%;
				height: 100%;
				margin: 0;
				padding: 0;
				background-color: #000;
			}
			ul > li:hover a {
				color: dodgerblue;
			}
			.grid-container {
				display: grid;
				grid-template-columns: 25% 25% 25% 25%;
				height: 100%;
				width: 100%;
				line-height: 80%;
				overflow:auto;
			}
			.grid-item {
				padding: 8px;
				text-align: center;
				border-radius: 5px;
				border: 2px solid rgba(100,100,100,0.3);
				margin: 5px;
				background-color: rgba(150, 150, 90, 0.9);
				text-decoration: none;
				font-size: 14pt;
			}
			.grid-item:hover {
				background-color: white;
				box-shadow: inset 0px 0px 30px rgba(0, 65, 65, 0.7);
			}
			.top-bar {
				background-color: #000;
				color: white;
			}
			.link {
				position: relative;
				padding: 3px;
				text-decoration: none;
				cursor: pointer;
				border-radius: 5px;
				color: white;
				background-color: dodgerblue;
				border: 1px solid #aaa;
				margin: 3px;
				margin-top:8px;
				box-shadow: dodgerblue 3px 3px 10px;
			}
			.link:hover {
				top: 5px;
			}
			.menu {
				position: relative;
			}
			.menu > .filemenu {
				position: absolute;
				top: 16px;
				z-index: 9999;
				display: none;
				min-height: 100px;
				max-height: 400px;
				overflow: scroll;
				width: 320px;
				border-radius: 10px;
				background-color: rgba(99,99,99, 0.85);
				box-shadow: rgba(100,100,100,0.3);
			}
			.menu:hover > .filemenu {
				display: block;
				line-height: 125%;
			}
			.menu > .filemenu > table.table {}
			.menu > .filemenu table.table tr td a {
				border-radius: 5px;
				display: block;
				width: 100%;
				margin:2px;
				padding:2px;
				font-size:11pt;
				text-decoration: none;
				background-color: rgba(0, 100, 230, 0.4);
				color: white;
			}
		</style>
		<meta name="viewport" content="width=device-width, initial-scale=1">
	</head>
	<body>
<?php
	$mode = cleanFilename($_GET['f']);
	$dir = "./"; $w = "100%"; $h = "100%";
	$ext = "swf";
	$exts = [];
	$files = [];
	$t_start = microtime();
	foreach(scandir($dir) as $f) {
		if($f == "." || $f == ".." || substr($f, -3) != $ext) continue;
		$files[] = array(
			"path" => $f,
			"size" => FileSizeConvert(filesize($f)),
			"shortname" => truncateStr($f, 25),
			"active" => (($f==$mode) ? 1 : 0)
		);
	}
	$t_finish = microtime();
	if(!$mode||!file_exists($mode)) { ?>
		<div class="top-bar">
			<p><strong>Filenames</strong> are truncated to the first 25 characters. File listing generated in <?=($t_finish-$t_start) ?> ms
		</div>
		<div class="grid-container">
<?php foreach($files as $file) { ?>
					<a class="grid-item" href="/index.php?f=<?=$file['path'] ?>" title="<?=$file['path'] ?>">
						<p><?=$file['shortname'] ?></p>
						<p>swf - <?=$file['size'] ?></p>
					</a>
<?php } ?>
		</div>
<?php } else { $mode = str_replace("/", "", $mode); ?>
		<style type="text/css">
			body {overflow: hidden;}
		</style>
		<div class="controls" style="height:32px;">
			<a class="link menu" href="#">
				Files...
				<div class="filemenu">
					<table border="0" cellspacing="0" cellpadding="0" class="table">
<?php foreach($files as $file) { ?>

						<tr style="<?=(($file['active']==1)?"display:none":"") ?>">
							<td>
								<a class="menu-item" href="/index.php?f=<?=$file['path'] ?>" title="<?=$file['path'] ?>">
									<?=$file['shortname'] ?> (<?=$file['size'] ?> swf)
								</a>
							</td>
						</tr>
<?php } ?>
					</table>
				</div>
			</a>
			<a class="link" href="/">
				Go back
			</a>
			<a class="link chide" href="#">
				Hide (CTRL+C)
			</a>
		</div>
		<div style="width:100%;height:100%;" class="swfwindow">
			<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
			codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0"
			WIDTH="<?=$w ?>"
			HEIGHT="<?=$h ?>"
			>
			<PARAM NAME="movie" VALUE="<?=$mode ?>">
			<PARAM NAME="quality" VALUE="high">
			<PARAM NAME="bgcolor" VALUE="#000000">
			<EMBED src="<?=$mode ?>"
				quality="high"
				bgcolor="#000000"
				WIDTH="<?=$w ?>"
				HEIGHT="<?=$h ?>"
				TYPE="application/x-shockwave-flash"
				PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer">
			</EMBED>
			</OBJECT>
		</div>
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script>
			!function(e,t){"object"==typeof exports&&"undefined"!=typeof module?module.exports=t():"function"==typeof define&&define.amd?define(t):e.hotkeys=t()}(this,function(){"use strict";var e="undefined"!=typeof navigator&&0<navigator.userAgent.toLowerCase().indexOf("firefox");function c(e,t,n){e.addEventListener?e.addEventListener(t,n,!1):e.attachEvent&&e.attachEvent("on"+t,function(){n(window.event)})}function l(e,t){for(var n=t.slice(0,t.length-1),o=0;o<n.length;o++)n[o]=e[n[o].toLowerCase()];return n}function s(e){e||(e="");for(var t=(e=e.replace(/\s/g,"")).split(","),n=t.lastIndexOf("");0<=n;)t[n-1]+=",",t.splice(n,1),n=t.lastIndexOf("");return t}function d(e,t){for(var n=e.length<t.length?t:e,o=e.length<t.length?e:t,r=!0,i=0;i<n.length;i++)-1===o.indexOf(n[i])&&(r=!1);return r}for(var t={backspace:8,tab:9,clear:12,enter:13,return:13,esc:27,escape:27,space:32,left:37,up:38,right:39,down:40,del:46,delete:46,ins:45,insert:45,home:36,end:35,pageup:33,pagedown:34,capslock:20,"\u21ea":20,",":188,".":190,"/":191,"`":192,"-":e?173:189,"=":e?61:187,";":e?59:186,"'":222,"[":219,"]":221,"\\":220},p={"\u21e7":16,shift:16,"\u2325":18,alt:18,option:18,"\u2303":17,ctrl:17,control:17,"\u2318":e?224:91,cmd:e?224:91,command:e?224:91},u=[],h={16:"shiftKey",18:"altKey",17:"ctrlKey"},v={16:!1,18:!1,17:!1},g={},n=1;n<20;n++)t["f"+n]=111+n;var o="all",y=v[e?224:91]=!(h[e?224:91]="metaKey"),w=function(e){return t[e.toLowerCase()]||p[e.toLowerCase()]||e.toUpperCase().charCodeAt(0)};function i(e){o=e||"all"}function m(){return o||"all"}function O(e,t,n){var o=void 0;if(t.scope===n||"all"===t.scope){for(var r in o=0<t.mods.length,v)Object.prototype.hasOwnProperty.call(v,r)&&(!v[r]&&-1<t.mods.indexOf(+r)||v[r]&&-1===t.mods.indexOf(+r))&&(o=!1);(0!==t.mods.length||v[16]||v[18]||v[17]||v[91])&&!o&&"*"!==t.shortcut||!1===t.method(e,t)&&(e.preventDefault?e.preventDefault():e.returnValue=!1,e.stopPropagation&&e.stopPropagation(),e.cancelBubble&&(e.cancelBubble=!0))}}function b(e,t,n){var o=s(e),r=[],i="all",a=document,f=0;for(void 0===n&&"function"==typeof t&&(n=t),"[object Object]"===Object.prototype.toString.call(t)&&(t.scope&&(i=t.scope),t.element&&(a=t.element)),"string"==typeof t&&(i=t);f<o.length;f++)r=[],1<(e=o[f].split("+")).length&&(r=l(p,e)),(e="*"===(e=e[e.length-1])?"*":w(e))in g||(g[e]=[]),g[e].push({scope:i,mods:r,shortcut:o[f],method:n,key:o[f]});void 0===a||y||(y=!0,c(a,"keydown",function(e){!function(e){var t=g["*"],n=e.keyCode||e.which||e.charCode;if(-1===u.indexOf(n)&&u.push(n),93!==n&&224!==n||(n=91),n in v){for(var o in v[n]=!0,p)p[o]===n&&(b[o]=!0);if(!t)return}for(var r in v)Object.prototype.hasOwnProperty.call(v,r)&&(v[r]=e[h[r]]);if(b.filter.call(this,e)){var i=m();if(t)for(var a=0;a<t.length;a++)t[a].scope===i&&O(e,t[a],i);if(n in g)for(var f=0;f<g[n].length;f++)O(e,g[n][f],i)}}(e)}),c(a,"keyup",function(e){!function(e){var t=e.keyCode||e.which||e.charCode,n=u.indexOf(t);if(n<0||u.splice(n,1),93!==t&&224!==t||(t=91),t in v)for(var o in v[t]=!1,p)p[o]===t&&(b[o]=!1)}(e)}))}var r={setScope:i,getScope:m,deleteScope:function(e,t){var n=void 0,o=void 0;for(var r in e||(e=m()),g)if(Object.prototype.hasOwnProperty.call(g,r))for(n=g[r],o=0;o<n.length;)n[o].scope===e?n.splice(o,1):o++;m()===e&&i(t||"all")},getPressedKeyCodes:function(){return u.slice(0)},isPressed:function(e){return"string"==typeof e&&(e=w(e)),-1!==u.indexOf(e)},filter:function(e){var t=e.target||e.srcElement,n=t.tagName;return!("INPUT"===n||"SELECT"===n||"TEXTAREA"===n||t.isContentEditable)},unbind:function(e,t,n){var o=s(e),r=void 0,i=[],a=void 0;"function"==typeof t&&(n=t,t="all");for(var f=0;f<o.length;f++){if(1<(r=o[f].split("+")).length&&(i=l(p,r)),e="*"===(e=r[r.length-1])?"*":w(e),t||(t=m()),!g[e])return;for(var c=0;c<g[e].length;c++){if(a=g[e][c],n&&a.method!==n)return;a.scope===t&&d(a.mods,i)&&(g[e][c]={})}}}};for(var a in r)Object.prototype.hasOwnProperty.call(r,a)&&(b[a]=r[a]);if("undefined"!=typeof window){var f=window.hotkeys;b.noConflict=function(e){return e&&window.hotkeys===b&&(window.hotkeys=f),b},window.hotkeys=b}return b});
		</script>
		<script type="text/javascript">
			var controlsShown = true;
			$(document).ready(() => {
				resize();
				hotkeys('ctrl+c', (e,h) => {
					if(h.key=="ctrl+c") {
						controlsShown=!controlsShown;
						resize();
					}
				});
				$(".chide").click(() => {
					hideControls();
				});
			});
			$(window).resize(() => {
				resize();
			});
			function resize() {
				var w=$(window).width(),h=$(window).height(), oh = $(".controls").outerHeight(true);
				if(!controlsShown) {$(".controls").hide();}else{$(".controls").show()}
				h = ((controlsShown) ? (h-oh) : h);
				$('.swfwindow');
				$("object").attr("width", w).attr("height", h);
				$("embed").attr("width", w).attr("height", h);
			}
			function hideControls() {
				controlsShown = false;
				resize();
			}
		</script>
<?php } ?>
	</body>
</html>
