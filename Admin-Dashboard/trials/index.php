<!DOCTYPE html>
<html lang="en">
<head>
    <title>Interactive Teeth Model</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <style>
        /* Tooltip CSS */
        #tooltip {
            position: absolute;
            padding: 10px;
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            border-radius: 5px;
            display: none; /* Hidden by default */
            pointer-events: none; /* Allow interactions with the 3D model */
            z-index: 10;
        }
    </style>
</head>
<body>

<!-- Tooltip HTML -->
<div id="tooltip"></div>

<script src="bower_components/three.js/three.min.js"></script>
<script src="STLLoader.js"></script>
<script src='TrackballControls.js'></script>

<script>
    var container;
    var camera, cameraTarget, scene, renderer;
    var raycaster = new THREE.Raycaster(); // Detect mouse clicks and hovers
    var mouse = new THREE.Vector2(); // Mouse position
    var selectedTeeth = []; // Array to store selected teeth
    var hoveredTooth = null; // Store the hovered tooth
    var tooltip = document.getElementById('tooltip'); // Tooltip element
    var clock = new THREE.Clock(); // Clock for the pulsing animation

    init();
    animate();

    function init() {
        container = document.createElement('div');
        document.body.appendChild(container);

        camera = new THREE.PerspectiveCamera(65, window.innerWidth / window.innerHeight, 1, 12);
        camera.position.set(1, -4, 2);

        // MOUSE CONTROLS
        controls = new THREE.TrackballControls(camera);
        controls.rotateSpeed = 2.0;
        controls.zoomSpeed = 2.2;
        controls.panSpeed = 0.8;
        controls.noZoom = false;
        controls.noPan = false;
        controls.staticMoving = true;
        controls.dynamicDampingFactor = 0.3;
        controls.keys = [65, 83, 68];
        controls.addEventListener('change', render);
        cameraTarget = new THREE.Vector3(0, -0.25, 0);

        scene = new THREE.Scene();
        scene.background = new THREE.Color(0xFFFFFF);
        var loader = new THREE.STLLoader();
        var material = new THREE.MeshPhongMaterial({ color: 0xAAAAAA, specular: 0xffffff, shininess: 100 });

        // Load and position each tooth
        loadTooth('incisor', '3d-teeth/teeth/teeth1.stl', {x: 0, y: 0, z: 0});
        loadTooth('canine', '3d-teeth/teeth/teeth2.stl', {x: 0, y: 0, z: 0});
        loadTooth('molar', '3d-teeth/teeth/teeth3.stl', {x: 0, y: 0, z: 0});
        loadTooth('', '3d-teeth/teeth/teeth4.stl', {x: 0, y: 0, z: 0});
        loadTooth('', '3d-teeth/teeth/teeth5.stl', {x: 0, y: 0, z: 0});
        loadTooth('', '3d-teeth/teeth/teeth6.stl', {x: 0, y: 0, z: 0});
        loadTooth('', '3d-teeth/teeth/teeth7.stl', {x: 0, y: 0, z: 0});
        loadTooth('', '3d-teeth/teeth/teeth8.stl', {x: 0, y: 0, z: 0});
        loadTooth('', '3d-teeth/teeth/teeth9.stl', {x: 0, y: 0, z: 0});
        loadTooth('', '3d-teeth/teeth/teeth10.stl', {x: 0, y: 0, z: 0});
        loadTooth('', '3d-teeth/teeth/teeth11.stl', {x: 0, y: 0, z: 0});
        loadTooth('', '3d-teeth/teeth/teeth12.stl', {x: 0, y: 0, z: 0});
        loadTooth('', '3d-teeth/teeth/teeth13.stl', {x: 0, y: 0, z: 0});
        loadTooth('', '3d-teeth/teeth/teeth14.stl', {x: 0, y: 0, z: 0});
        loadTooth('', '3d-teeth/teeth/teeth15.stl', {x: 0, y: 0, z: 0});
        loadTooth('', '3d-teeth/teeth/teeth16.stl', {x: 0, y: 0, z: 0});
        loadTooth('', '3d-teeth/teeth/teeth17.stl', {x: 0, y: 0, z: 0});
        loadTooth('', '3d-teeth/teeth/teeth18.stl', {x: 0, y: 0, z: 0});
        loadTooth('', '3d-teeth/teeth/teeth19.stl', {x: 0, y: 0, z: 0});
        loadTooth('', '3d-teeth/teeth/teeth20.stl', {x: 0, y: 0, z: 0});
        loadTooth('', '3d-teeth/teeth/teeth21.stl', {x: 0, y: 0, z: 0});
        loadTooth('', '3d-teeth/teeth/teeth22.stl', {x: 0, y: 0, z: 0});
        loadTooth('', '3d-teeth/teeth/teeth23.stl', {x: 0, y: 0, z: 0});
        loadTooth('', '3d-teeth/teeth/teeth24.stl', {x: 0, y: 0, z: 0});
        loadTooth('', '3d-teeth/teeth/teeth25.stl', {x: 0, y: 0, z: 0});
        loadTooth('', '3d-teeth/teeth/teeth26.stl', {x: 0, y: 0, z: 0});
        loadTooth('', '3d-teeth/teeth/teeth27.stl', {x: 0, y: 0, z: 0});
        loadTooth('', '3d-teeth/teeth/teeth28.stl', {x: 0, y: 0, z: 0});
        loadTooth('', '3d-teeth/teeth/teeth29.stl', {x: 0, y: 0, z: 0});
        loadTooth('', '3d-teeth/teeth/teeth30.stl', {x: 0, y: 0, z: 0});
        loadTooth('', '3d-teeth/teeth/teeth31.stl', {x: 0, y: 0, z: 0});
        loadTooth('', '3d-teeth/teeth/teeth32.stl', {x: 0, y: 0, z: 0});

        // LIGHTS
        scene.add(new THREE.HemisphereLight(0xffffff, 0x111122));

        // RENDERER
        renderer = new THREE.WebGLRenderer({ antialias: true });
        renderer.setPixelRatio(window.devicePixelRatio);
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.gammaInput = true;
        renderer.gammaOutput = true;

        container.appendChild(renderer.domElement);

        window.addEventListener('resize', onWindowResize, false);
        window.addEventListener('mousemove', onDocumentMouseMove, false); // Add hover event listener
        window.addEventListener('click', onDocumentMouseDown, false); // Add click event listener
    }

    function loadTooth(name, stlPath, position) {
        var loader = new THREE.STLLoader();
        var material = new THREE.MeshPhongMaterial({ color: 0xAAAAAA, specular: 0xffffff, shininess: 100 });
        loader.load(stlPath, function (geometry) {
            var toothMesh = new THREE.Mesh(geometry, material);
            toothMesh.scale.set(0.3, 0.3, 0.3);
            toothMesh.name = name; // Assign name to the tooth
            toothMesh.position.set(position.x, position.y, position.z); // Position the tooth
            scene.add(toothMesh);
        });
    }

    function onWindowResize() {
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
        controls.handleResize();
    }

    function animate() {
        requestAnimationFrame(animate);

        // Get elapsed time for the pulsing animation
        var elapsedTime = clock.getElapsedTime();

        // Animate the selected teeth color to pulse between red and a lighter red
        selectedTeeth.forEach(function(tooth) {
            var pulseFactor = (Math.sin(elapsedTime * 2) + 1) / 2; // Value between 0 and 1
            var newColor = new THREE.Color(1, pulseFactor * 0.5, pulseFactor * 0.5); // Pulsing red color
            tooth.material.color.set(newColor); // Apply the new color
        });

        render();
        controls.update();
    }

    function onDocumentMouseMove(event) {
        // Calculate mouse position in normalized device coordinates
        mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
        mouse.y = - (event.clientY / window.innerHeight) * 2 + 1;

        // Update raycaster to check hover
        raycaster.setFromCamera(mouse, camera);
        var intersects = raycaster.intersectObjects(scene.children, true);

        if (intersects.length > 0) {
            var currentTooth = intersects[0].object;

            // Only change color if it's a different tooth and not selected
            if (hoveredTooth !== currentTooth && !selectedTeeth.includes(currentTooth)) {
                if (hoveredTooth && !selectedTeeth.includes(hoveredTooth)) {
                    hoveredTooth.material.color.set(0xAAAAAA); // Reset previous hover color if not selected
                }
                currentTooth.material.color.set(0xFFFF00); // Highlight hovered tooth in yellow
                hoveredTooth = currentTooth; // Update hovered tooth
            }

            // Update tooltip position and content
            updateTooltip(currentTooth, event.clientX, event.clientY);
        } else {
            // Reset hover color if not selected and hide tooltip
            if (hoveredTooth && !selectedTeeth.includes(hoveredTooth)) {
                hoveredTooth.material.color.set(0xAAAAAA); // Reset color if not selected
            }
            hoveredTooth = null;
            tooltip.style.display = 'none';
        }
    }

    function updateTooltip(tooth, x, y) {
        let description = "";
        switch (tooth.name) {
            case 'incisor':
                description = "Tooth: Incisor. Function: Cutting.";
                break;
            case 'canine':
                description = "Tooth: Canine. Function: Tearing.";
                break;
            case 'molar':
                description = "Tooth: Molar. Function: Grinding.";
                break;
            default:
                description = "Unknown tooth.";
        }

        // Update tooltip content
        tooltip.innerHTML = description;

        // Position the tooltip near the mouse
        tooltip.style.left = (x + 10) + 'px'; // Offset to avoid overlapping the cursor
        tooltip.style.top = (y + 10) + 'px';
        tooltip.style.display = 'block'; // Show the tooltip
    }

    function onDocumentMouseDown(event) {
        mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
        mouse.y = - (event.clientY / window.innerHeight) * 2 + 1;

        raycaster.setFromCamera(mouse, camera);

        var intersects = raycaster.intersectObjects(scene.children, true);

        if (intersects.length > 0) {
            var clickedTooth = intersects[0].object;

            // Toggle selection
            if (selectedTeeth.includes(clickedTooth)) {
                clickedTooth.material.color.set(0xAAAAAA); // Deselect tooth
                selectedTeeth = selectedTeeth.filter(tooth => tooth !== clickedTooth); // Remove from selected array
            } else {
                clickedTooth.material.color.set(0xFF0000); // Highlight clicked tooth in red
                selectedTeeth.push(clickedTooth); // Add to selected teeth array
            }

            displayToothDetails(clickedTooth); // Display the tooth's details
        }
    }

    function displayToothDetails(tooth) {
        let details = "";
        switch (tooth.name) {
            case 'incisor':
                details = "Tooth: Incisor. Function: Cutting.";
                break;
            case 'canine':
                details = "Tooth: Canine. Function: Tearing.";
                break;
            case 'molar':
                details = "Tooth: Molar. Function: Grinding.";
                break;
            default:
                details = "Unknown tooth.";
        }
        alert(details); // Simple alert for now; replace with custom UI if needed
    }

    function render() {
        camera.lookAt(cameraTarget);
        renderer.render(scene, camera);
    }
</script>

</body>
</html>
