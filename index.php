<!DOCTYPE html>
<html lang="en">
	<head>
		<title>three.js webgl - STL</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
		<style>
			body {
				font-family: Monospace;
				background-color: #000000;
				margin: 0px;
				overflow: hidden;
			}

			#info {
				color: #fff;
				position: absolute;
				top: 10px;
				width: 100%;
				text-align: center;
				z-index: 100;
				display:block;

			}

			a { color: skyblue }
			.button { background:#999; color:#eee; padding:0.2em 0.5em; cursor:pointer }
			.highlight { background:orange; color:#fff; }

			span {
				display: inline-block;
				width: 60px;
				float: left;
				text-align: center;
			}

		</style>
	</head>
	<body>
		<div id="info">
			<?php
				if ($handle = opendir('./scans')) {
					$i = 0;
					while (false !== ($file = readdir($handle)))
					{
						if (($file != ".") 
						&& ($file != ".."))
						{
							if ($i == 0)
							{
								$firstFile = $file;
							}
							$thelist .= '<option value="'.$file.'">'.$file.'</option>';
							$i++;
						}
					}

					closedir($handle);
				}
			?>
			<select onchange="clearScene(); init(this.value)">
				<?=$thelist?>
			</select>
			<a id="downloadLink" href="#" download="Download Your 3d Model!">Download Your 3d Model!</a>
		</div>
		<script src="js/threejs/three.min.js"></script>
		<script src="js/threejs/loaders/STLLoader.js"></script>
		<script src="js/threejs/controls/OrbitControls.js"></script>		
		<script src="js/threejs/Detector.js"></script>
		<script src="js/threejs/libs/stats.min.js"></script>
		<script>
			//init
			if ( ! Detector.webgl ) Detector.addGetWebGLMessage();
			var container, stats;
			var camera, cameraTarget, scene, renderer;
			var modelUrl;
			var stlmesh;
			document.getElementById('downloadLink').href = modelUrl;
			init('<?php echo $firstFile; ?>');
			animate();
			function clearScene() {
				scene.remove(stlmesh);
			}
			function init(stlFile) {
				if (container)
				{
					document.body.removeChild( container );
				}
				//file list
		        modelUrl = 'scans/' + stlFile;
		        document.getElementById('downloadLink').href = modelUrl;
		        //threejs container init
				container = document.createElement( 'div' );
				document.body.appendChild( container );
				camera = new THREE.PerspectiveCamera( 35, window.innerWidth / window.innerHeight, 1, 15 );
				camera.position.set( 6, .4, 6 );
				//camera.position.z = 300;
				//camera.position.y = 150;
				cameraTarget = new THREE.Vector3( 0, -0.25, 0 );
				scene = new THREE.Scene();
				//threejs model loading
				var loader = new THREE.STLLoader();
				loader.addEventListener( 'load', function ( event ) {
					var geometry = event.content;
					var material = new THREE.MeshNormalMaterial();
					stlmesh = new THREE.Mesh( geometry, material );
					stlmesh.position.set( 0, - 0.25, 0.6 );
					stlmesh.rotation.set( 0, - Math.PI / 2, 0 );
					stlmesh.scale.set( 0.75, 0.75, 0.75 );
					scene.add( stlmesh );
				} );
				loader.load( modelUrl );
				// renderer
				renderer = new THREE.WebGLRenderer( { antialias: true } );
				renderer.setSize( window.innerWidth, window.innerHeight );
				renderer.gammaInput = true;
				renderer.gammaOutput = true;
				renderer.shadowMapEnabled = true;
				renderer.shadowMapCullFace = THREE.CullFaceBack;
				container.appendChild( renderer.domElement );
				// stats
				/*
				stats = new Stats();
				stats.domElement.style.position = 'absolute';
				stats.domElement.style.bottom = '0px';
				container.appendChild( stats.domElement );
				*/
				// keep it big
				window.addEventListener( 'resize', onWindowResize, false );
				//setControls();
			}
			function setControls(){
				//var radius = sphere.geometry.boundingSphere.radius;
				controls = new THREE.OrbitControls( camera );
				controls.target = new THREE.Vector3( 0, 0, 0 );
				//controls.noPan = true;
				//controls.noRotate = true;
				controls.update();
			}
			function onWindowResize() {
				camera.aspect = window.innerWidth / window.innerHeight;
				camera.updateProjectionMatrix();
				renderer.setSize( window.innerWidth, window.innerHeight );
			}
			function animate() {
				requestAnimationFrame( animate );
				render();
				//stats.update();
			}
			function render() {
				var timer = Date.now() * -.001;
				
				camera.position.x = Math.cos( timer ) * 10;
				camera.position.z = Math.sin( timer ) * 10;
				camera.lookAt( cameraTarget );
				renderer.render( scene, camera );
				
			}
		</script>
	</body>
</html>
