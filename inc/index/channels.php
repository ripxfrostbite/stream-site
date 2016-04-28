<!--<div class="jumbotron">
	<div class="container">
		<h1><i class="fa fa-video-camera"></i> Live Channels</h1>
	</div>
</div>-->
<div class="container">
	<div class="row">
		<div class="col-md-12 text-right">
			<div class="btn-group">
				<a href="#" class="display-grid btn btn-default"><span class="fa fa-th-large"></span> Grid</a>
				<a href="#" class="display-list btn btn-default"><span class="fa fa-th-list"></span> List</a>
			</div>
		</div>
	</div>
	<?php
	if (count($rtmpinfo["rtmp"]["channels"]) > 0) {
		$channels = array();
		foreach ($rtmpinfo["rtmp"]["channels"] as $channelName => $streamkey) {
			$channels[$channelName] = $streamkey;
			$channels[$channelName]["screenshot"] = 'img/thumb_' . $channelName . '.png';
			if (!file_exists($channels[$channelName]["screenshot"])) {
				$channels[$channelName]["screenshot"] = 'img/no-preview.jpg';
			}

			$mediainfo = array();
			$channels[$channelName]["mediainfo"] = $mediainfo;
		}
		?>
		<div class="row grid">
			<?php
			foreach ($channels as $channelName => $streamkey) {
				$viewcount = file_get_contents($furl . '/nclients?app=live&name=' . $channelName);
				$cname = $user->updateStreamkey($channelName, 'channel');
				$ctitle = $user->updateStreamkey($channelName, 'title');
				$col = 'col-md-6';
				if (count($rtmpinfo["rtmp"]["channels"]) === 1) {
					$col.= ' col-md-offset-3';
				}
				echo '<div class="grid ' . $col . '">' . "\r\n";
				echo '		<a href="?channel=' . $channelName . '"><figure class="effect-sarah">';
				echo "			<img src='" . $streamkey['screenshot'] . "' alt='" . $cname . "'/>\r\n";
				echo "			<figcaption>\r\n";
				echo "				<h2>" . $cname . "</h2>\r\n";
				echo "				<p>" . $ctitle . "</p>\r\n";
				echo '				<div><label class="viewcount">Viewers: ' . $viewcount . "</label><div>\r\n";
				echo "			</figcaption>\r\n";
				echo "		</figure></a>\r\n";
				echo '</div>' . "\r\n";
			}
			?>
		</div>
		<div class="row list">
			<table class="table channels">
				<thead>
					<tr>
						<th>Channel</th>
						<th>Duration</th>
						<th>Viewers</th>
						<th class="hidden-xs">Definition</th>
						<th class="text-center" style="width:100px">Record</th>
						<th class="text-center" style="width:100px">Watch</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($channels as $channelName => $streamkey) {
						$seed = uniqid();
						$viewcount = file_get_contents($furl . '/nclients?app=live&name=' . $channelName);
						echo '<tr>' . "\r\n";
						echo '	<td><a href="?channel=' . $channelName . '">' . $user->updateStreamkey($channelName, 'channel') . '</a></td>' . "\r\n";
						echo '	<td>' . gmdate("H:i:s", ($streamkey["time"] / 1000)) . '</td>' . "\r\n";
						echo '	<td>' . $viewcount . ' watching</td>';
						echo '	<td class="hidden-xs">' . "\r\n";
						echo '		<em data-toggle="popover" data-seed="' . $seed . '" data-trigger="hover" data-placement="top" data-title="Meta Data">' . $streamkey["meta"]["video"]["height"] . 'p@' . $streamkey["meta"]["video"]["frame_rate"] . 'fps</em>' . "\r\n";
						echo '	</td>' . "\r\n";
						echo '	<td class="text-center"><a class="btn btn-record btn-danger" data-channel="' . $channelName . '"><i class="fa fa-circle"></i><i class="fa fa-stop"></i></a></td>' . "\r\n";
						echo '	<td class="text-center"><a class="btn btn-play btn-primary" href="?channel=' . $channelName . '"><i class="fa fa-play"></i></a></td>' . "\r\n";
						echo '</tr>' . "\r\n";
						echo '<div id="popover-' . $seed . '" class="hidden">';
						echo '	<h4>Video</h4>';
						echo '	<ul>';
						echo '		<li>Codec: ' . $streamkey["meta"]["video"]["codec"] . ' ' . $streamkey["meta"]["video"]["profile"] . '</li>';
						echo '		<li>Bitrate: <span data-size="' . $streamkey["bw_video"] . '"></span>b/s</li>';
						echo '		<li>Definition: ' . $streamkey["meta"]["video"]["width"] . '*' . $streamkey["meta"]["video"]["height"] . '</li>';
						echo '		<li>Framerate: ' . $streamkey["meta"]["video"]["frame_rate"] . ' fps</li>';
						echo '	</ul>';
						echo '	<h4>Audio</h4>';
						echo '	<ul>';
						echo '		<li>Codec: ' . $streamkey["meta"]["audio"]["codec"] . ' ' . $streamkey["meta"]["audio"]["profile"] . '</li>';
						echo '		<li>Bitrate: <span data-size="' . $streamkey["bw_audio"] . '"></span>b/s</li>';
						echo '		<li>Sample Rate: ' . $streamkey["meta"]["audio"]["sample_rate"] . ' Hz</li>';
						echo '		<li>Channels: ' . $streamkey["meta"]["audio"]["channels"] . '</li>';
						echo '	</ul>';
						echo '</div>' . "\r\n";
					}
					?>
				</tbody>
			</table>
		</div>
		<?php
	} else {
		?>
		<div class="col-md-12">
			<p class="text-center">No channel available.</p>
			<br />
			<p class="text-center"><a class="btn btn-lg btn-primary" href="?channels"><i class="fa fa-refresh"></i> Refresh</a></p>
		</div>
		<?php
	}
	?>
</div>