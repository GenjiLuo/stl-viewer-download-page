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

		<script src="js/three.min.js"></script>

		<script src="js/loaders/STLLoader.js"></script>

		<script src="js/Detector.js"></script>
		<script src="js/libs/stats.min.js"></script>

		<script>

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
        
        modelUrl = 'scans/' + stlFile;
        document.getElementById('downloadLink').href = modelUrl;
        
				container = document.createElement( 'div' );
				document.body.appendChild( container );

				camera = new THREE.PerspectiveCamera( 35, window.innerWidth / window.innerHeight, 1, 15 );
				camera.position.set( 3, 0.15, 3 );

				cameraTarget = new THREE.Vector3( 0, -0.25, 0 );

				scene = new THREE.Scene();
				scene.fog = new THREE.Fog( 0x686868, 2, 15 );


				// Ground

				var plane = new THREE.Mesh( new THREE.PlaneGeometry( 40, 40 ), new THREE.MeshPhongMaterial( { ambient: 0x686868, color: 0x000000, specular: 0x101010 } ) );
				plane.rotation.x = -Math.PI/2;
				plane.position.y = -0.5;
				scene.add( plane );

				plane.receiveShadow = true;


				// ASCII file

				var loader = new THREE.STLLoader();
				loader.addEventListener( 'load', function ( event ) {

					var geometry = event.content;
					var material = new THREE.MeshPhongMaterial( { ambient: 0x53249e, color: 0x53249e, specular: 0x111111, shininess: 200 } );
					stlmesh = new THREE.Mesh( geometry, material );

					stlmesh.position.set( 0, - 0.25, 0.6 );
					stlmesh.rotation.set( 0, - Math.PI / 2, 0 );
					stlmesh.scale.set( 0.75, 0.75, 0.75 );

					stlmesh.castShadow = true;
					stlmesh.receiveShadow = true;

					scene.add( stlmesh );

				} );
				loader.load( modelUrl );

				// Lights

				scene.add( new THREE.AmbientLight( 0x999999 ) );

				addShadowedLight( 1, 1, 1, 0xffffff, 1.35 );
				addShadowedLight( 0.5, 1, -1, 0xffffff, 1 );

				// renderer

				renderer = new THREE.WebGLRenderer( { antialias: true } );
				renderer.setSize( window.innerWidth, window.innerHeight );

				renderer.setClearColor( scene.fog.color, 1 );

				renderer.gammaInput = true;
				renderer.gammaOutput = true;

				renderer.shadowMapEnabled = true;
				renderer.shadowMapCullFace = THREE.CullFaceBack;

				container.appendChild( renderer.domElement );

				// stats

				stats = new Stats();
				stats.domElement.style.position = 'absolute';
				stats.domElement.style.bottom = '0px';
				container.appendChild( stats.domElement );

				//

				window.addEventListener( 'resize', onWindowResize, false );

			}

			function addShadowedLight( x, y, z, color, intensity ) {

				var directionalLight = new THREE.DirectionalLight( color, intensity );
				directionalLight.position.set( x, y, z )
				scene.add( directionalLight );

				directionalLight.castShadow = true;
				// directionalLight.shadowCameraVisible = true;

				var d = 1;
				directionalLight.shadowCameraLeft = -d;
				directionalLight.shadowCameraRight = d;
				directionalLight.shadowCameraTop = d;
				directionalLight.shadowCameraBottom = -d;

				directionalLight.shadowCameraNear = 1;
				directionalLight.shadowCameraFar = 4;

				directionalLight.shadowMapWidth = 1024;
				directionalLight.shadowMapHeight = 1024;

				directionalLight.shadowBias = -0.005;
				directionalLight.shadowDarkness = 0.15;

			}

			function onWindowResize() {

				camera.aspect = window.innerWidth / window.innerHeight;
				camera.updateProjectionMatrix();

				renderer.setSize( window.innerWidth, window.innerHeight );

			}

			function animate() {

				requestAnimationFrame( animate );

				render();
				stats.update();

			}

			function render() {

				var timer = Date.now() * 0.0005;

				camera.position.x = Math.cos( timer ) * 3;
				camera.position.z = Math.sin( timer ) * 3;

				camera.lookAt( cameraTarget );

				renderer.render( scene, camera );

			}

		</script>
	</body>
</html>
