    document.getElementById('left-view').addEventListener('click', function() {
        moveCameraToPosition(new THREE.Vector3(-4, -3.5, 0.1)); // Left view
    });

    document.getElementById('right-view').addEventListener('click', function() {
        moveCameraToPosition(new THREE.Vector3(2, -2, 2)); // Right view
    });

    document.getElementById('original-view').addEventListener('click', function() {
        moveCameraToPosition(new THREE.Vector3(0, -3.5, 0.1)); // Original position
    });
    import * as THREE from 'three';
    import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
    import { STLLoader } from 'three/addons/loaders/STLLoader.js';


    var container;
    var controls;
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

        // Targeting the #teeth-container div for rendering
        container = document.getElementById('teeth-container');

        camera = new THREE.PerspectiveCamera(65, container.clientWidth / container.clientHeight, 2, 12);
        camera.position.set(0, -3.5, 0.1);

        // MOUSE CONTROLS
        // Replace TrackballControls with OrbitControls
        controls = new OrbitControls(camera, container);

        // Restrict rotation around the Y-axis (horizontal rotation)
        controls.enableRotate = true; // Allow rotation
        controls.enableZoom = true; // Allow zoom

        // Restrict vertical rotation
        controls.minPolarAngle = Math.PI / 2; // 90 degrees (horizontal)
        controls.maxPolarAngle = Math.PI / 2; // 90 degrees (horizontal)

        // Optionally, limit horizontal rotation (around Y-axis) if needed
        controls.minAzimuthAngle = -Infinity; // No limit on left/right rotation
        controls.maxAzimuthAngle = Infinity; // No limit on left/right rotation

        // Set other control parameters
        controls.rotateSpeed = 2.0;
        controls.zoomSpeed = 2.2;
        controls.enablePan = true; // Allow panning

        // Event listener for rendering
        controls.addEventListener('change', render);


        cameraTarget = new THREE.Vector3(0, -0.25, 0);

        scene = new THREE.Scene();

        scene.background = null;


        var loader = new STLLoader();
        var material = new THREE.MeshPhongMaterial({
            color: 0xAAAAAA,
            specular: 0xffffff,
            shininess: 100
        });

        // Load and position each tooth
        loadTooth('(R) Upper Third Molar', '3d-teeth/teeth/teeth1.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Lower Second Molar', '3d-teeth/teeth/teeth2.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(R) Upper First Molar', '3d-teeth/teeth/teeth3.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(R) Upper Second Bicuspid', '3d-teeth/teeth/teeth4.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(R) Upper First Bicuspid', '3d-teeth/teeth/teeth5.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(R) Upper Cuspid', '3d-teeth/teeth/teeth6.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(R) Upper Lateral Incisor', '3d-teeth/teeth/teeth7.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(R) Upper Central Incisor', '3d-teeth/teeth/teeth8.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Upper Central Incisor', '3d-teeth/teeth/teeth9.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Upper Lateral Incisor', '3d-teeth/teeth/teeth10.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Upper Cuspid', '3d-teeth/teeth/teeth11.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Upper First Bicuspid', '3d-teeth/teeth/teeth12.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Upper Second Bicuspid', '3d-teeth/teeth/teeth13.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Upper Fist Molar', '3d-teeth/teeth/teeth14.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Lower Second Molar', '3d-teeth/teeth/teeth15.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Upper Third Molar', '3d-teeth/teeth/teeth16.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(R) Upper Third Molar', '3d-teeth/teeth/teeth17.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Lower Second Molar', '3d-teeth/teeth/teeth18.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(R) Lower First Molar', '3d-teeth/teeth/teeth19.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(R) Lower Second Bicuspid', '3d-teeth/teeth/teeth20.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(R) Lower First Bicuspid', '3d-teeth/teeth/teeth21.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(R) Lower Cuspid', '3d-teeth/teeth/teeth22.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(R) Lower Lateral Incisor', '3d-teeth/teeth/teeth23.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(R) Lower Central Incisor', '3d-teeth/teeth/teeth24.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Lower Central Incisor', '3d-teeth/teeth/teeth25.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Lower Lateral Incisor', '3d-teeth/teeth/teeth26.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Lower Cuspid', '3d-teeth/teeth/teeth27.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Lower First Bicuspid', '3d-teeth/teeth/teeth28.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Lower Second Bicuspid', '3d-teeth/teeth/teeth29.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Lower First Molar', '3d-teeth/teeth/teeth30.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Lower Second Molar', '3d-teeth/teeth/teeth31.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        loadTooth('(L) Lower Third Molar', '3d-teeth/teeth/teeth32.stl', {
            x: 0,
            y: 0,
            z: 0
        });
        // Load r_letter.stl and set color to blue
        loadTooth('r_letter', '3d-teeth/teeth/r_letter.stl', {
            x: 0,
            y: 0,
            z: 0
        }, true, 0xCFE2F3);

        // Load l_letter.stl and set color to blue
        loadTooth('l_letter', '3d-teeth/teeth/l_letter.stl', {
            x: 0,
            y: 0,
            z: 0
        }, true, 0xCFE2F3);


        // LIGHTS
        // Hemisphere Light (ambient lighting)
        var hemisphereLight = new THREE.HemisphereLight(0xffffff, 0x444444, 1.2);
        hemisphereLight.position.set(0, 1, 0); // Light from above
        scene.add(hemisphereLight);

        // Directional Light (strong front light)
        var directionalLight = new THREE.DirectionalLight(0xffffff, 1.0);
        directionalLight.position.set(2, 1, 3); // Positioned to shine on the front
        directionalLight.castShadow = true;
        scene.add(directionalLight);

        // Fill light to reduce shadows from below
        var fillLight = new THREE.PointLight(0xffffff, 0.8);
        fillLight.position.set(0, -3, 3); // Soft light from below the model
        scene.add(fillLight);

        // RENDERER
        renderer = new THREE.WebGLRenderer({
            antialias: true,
            alpha: true // Enable transparency
        });
        renderer.setPixelRatio(window.devicePixelRatio);
        renderer.setSize(container.clientWidth, container.clientHeight);
        renderer.gammaInput = true;
        renderer.gammaOutput = true;

        container.appendChild(renderer.domElement);

        window.addEventListener('resize', onWindowResize, false);
        window.addEventListener('mousemove', onDocumentMouseMove, false); // Add hover event listener
        window.addEventListener('click', onDocumentMouseDown, false); // Add click event listener
    }

    function loadTooth(name, stlPath, position, isLetter = false, color = 0xAAAAAA) {
        var loader = new STLLoader();
        var material = new THREE.MeshPhongMaterial({
            color: color, // Use the custom color
            specular: 0xffffff,
            shininess: 100
        });
        loader.load(stlPath, function(geometry) {
            var toothMesh = new THREE.Mesh(geometry, material);
            toothMesh.scale.set(0.3, 0.3, 0.3);
            toothMesh.name = name; // Assign name to the tooth
            toothMesh.position.set(position.x, position.y, position.z); // Position the tooth
            toothMesh.isLetter = isLetter; // Mark as letter if true
            scene.add(toothMesh);
        });
    }



    function onWindowResize() {
        // Get the container dimensions
        const width = container.clientWidth;
        const height = container.clientHeight;
    
        // Update the camera's aspect ratio and projection matrix
        camera.aspect = width / height;
        camera.updateProjectionMatrix();
    
        // Resize the renderer to match the new container size
        renderer.setSize(width, height);
    
        // Remove the outdated handleResize() method call
        // controls.handleResize(); <- This can be safely removed
    }   

    // Listen for window resize events
    window.addEventListener('resize', onWindowResize, false);





    function moveCameraToPosition(targetPosition, targetLookAt = new THREE.Vector3(0, 0, 0)) {
        // Create a tweening effect for smooth camera movement
        var duration = 1000; // Duration of the animation (in milliseconds)
    
        // Use Tween.js or a custom tweening logic to move the camera's position
        new TWEEN.Tween(camera.position)
            .to({
                x: targetPosition.x,
                y: targetPosition.y,
                z: targetPosition.z
            }, duration)
            .easing(TWEEN.Easing.Quadratic.InOut) // Smooth easing
            .onUpdate(() => {
                // Optionally, update the render loop while the camera is moving
                render();
            })
            .start();
    
        // Similarly, tween the target (camera.lookAt) to the desired point
        new TWEEN.Tween(cameraTarget)
            .to({
                x: targetLookAt.x,
                y: targetLookAt.y,
                z: targetLookAt.z
            }, duration)
            .easing(TWEEN.Easing.Quadratic.InOut)
            .onUpdate(() => {
                camera.lookAt(cameraTarget); // Continuously update the camera's target
                controls.target.copy(cameraTarget); // Update OrbitControls target
                controls.update(); // Recalculate controls
                render();
            })
            .start();
    }
    



    function onDocumentMouseMove(event) {
        // Calculate mouse position in normalized device coordinates relative to container
        const rect = container.getBoundingClientRect();
        mouse.x = ((event.clientX - rect.left) / container.clientWidth) * 2 - 1;
        mouse.y = -((event.clientY - rect.top) / container.clientHeight) * 2 + 1;

        // Update raycaster to check hover
        raycaster.setFromCamera(mouse, camera);
        var intersects = raycaster.intersectObjects(scene.children, true);

        if (intersects.length > 0) {
            var currentTooth = intersects[0].object;

            // Skip if the hovered object is a letter (r_letter or l_letter)
            if (currentTooth.isLetter) {
                tooltip.style.display = 'none'; // Hide tooltip for letters
                return; // Ignore letters
            }

            // Only change color if it's a different tooth and not selected
            if (hoveredTooth !== currentTooth && !selectedTeeth.includes(currentTooth)) {
                if (hoveredTooth && !selectedTeeth.includes(hoveredTooth)) {
                    hoveredTooth.material.color.set(0xAAAAAA); // Reset previous hover color if not selected
                }
                currentTooth.material.color.set(0xFFFF00); // Highlight hovered tooth in yellow
                hoveredTooth = currentTooth; // Update hovered tooth
            }

            // Update tooltip position and content
            updateTooltip(currentTooth);
        } else {
            tooltip.style.display = 'none'; // Hide tooltip if no tooth is hovered
            if (hoveredTooth && !selectedTeeth.includes(hoveredTooth)) {
                hoveredTooth.material.color.set(0xAAAAAA); // Reset color if not selected
            }
            hoveredTooth = null;
        }
    }

    function onDocumentMouseDown(event) {
        // Calculate mouse position relative to the container
        const rect = container.getBoundingClientRect();
        mouse.x = ((event.clientX - rect.left) / container.clientWidth) * 2 - 1;
        mouse.y = -((event.clientY - rect.top) / container.clientHeight) * 2 + 1;

        raycaster.setFromCamera(mouse, camera);
        var intersects = raycaster.intersectObjects(scene.children, true);

        if (intersects.length > 0) {
            var clickedTooth = intersects[0].object;

            // Skip if the clicked object is a letter (r_letter or l_letter)
            if (clickedTooth.isLetter) {
                return; // Ignore letters
            }

            // Toggle selection
            if (selectedTeeth.includes(clickedTooth)) {
                clickedTooth.material.color.set(0xAAAAAA); // Deselect tooth
                selectedTeeth = selectedTeeth.filter(tooth => tooth !== clickedTooth); // Remove from selected array
            } else {
                clickedTooth.material.color.set(0xFF0000); // Select tooth and color it red
                selectedTeeth.push(clickedTooth); // Add to selected array
            }
        }
    }



    function updateTooltip(tooth) {
        let description = "";
        switch (tooth.name) {
            case '(R) Upper Third Molar':
                description = "(R) Upper Third Molar (FDI: 18, Palmer: 8, Universal: 1)";
                break;
            case '(R) Upper Second Molar':
                description = "(R) Upper Second Molar (FDI: 17, Palmer: 7, Universal: 2)";
                break;
            case '(R) Upper First Molar':
                description = "(R) Upper First Molar (FDI: 16, Palmer: 6, Universal: 3)";
                break;
            case '(R) Upper Second Bicuspid':
                description = "(R) Upper Second Bicuspid (FDI: 15, Palmer: 5, Universal: 4)";
                break;
            case '(R) Upper First Bicuspid':
                description = "(R) Upper First Bicuspid (FDI: 14, Palmer: 4, Universal: 5)";
                break;
            case '(R) Upper Cuspid':
                description = "(R) Upper Cuspid (FDI: 13, Palmer: 3, Universal: 6)";
                break;
            case '(R) Upper Lateral Incisor':
                description = "(R) Upper Lateral Incisor (FDI: 12, Palmer: 2, Universal: 7)";
                break;
            case '(R) Upper Central Incisor':
                description = "(R) Upper Central Incisor (FDI: 11, Palmer: 1, Universal: 8)";
                break;
            case '(L) Upper Central Incisor':
                description = "(L) Upper Central Incisor (FDI: 21, Palmer: 1, Universal: 9)";
                break;
            case '(L) Upper Lateral Incisor':
                description = "(L) Upper Lateral Incisor (FDI: 22, Palmer: 2, Universal: 10)";
                break;
            case '(L) Upper Cuspid':
                description = "(L) Upper Cuspid (FDI: 23, Palmer: 3, Universal: 11)";
                break;
            case '(L) Upper First Bicuspid':
                description = "(L) Upper First Bicuspid (FDI: 24, Palmer: 4, Universal: 12)";
                break;
            case '(L) Upper Second Bicuspid':
                description = "(L) Upper Second Bicuspid (FDI: 25, Palmer: 5, Universal: 13)";
                break;
            case '(L) Upper First Molar':
                description = "(L) Upper First Molar (FDI: 26, Palmer: 6, Universal: 14)";
                break;
            case '(L) Upper Second Molar':
                description = "(L) Upper Second Molar (FDI: 27, Palmer: 7, Universal: 15)";
                break;
            case '(L) Upper Third Molar':
                description = "(L) Upper Third Molar (FDI: 28, Palmer: 8, Universal: 16)";
                break;
            case '(R) Lower Third Molar':
                description = "(R) Lower Third Molar (FDI: 48, Palmer: 8, Universal: 32)";
                break;
            case '(R) Lower Second Molar':
                description = "(R) Lower Second Molar (FDI: 47, Palmer: 7, Universal: 31)";
                break;
            case '(R) Lower First Molar':
                description = "(R) Lower First Molar (FDI: 46, Palmer: 6, Universal: 30)";
                break;
            case '(R) Lower Second Bicuspid':
                description = "(R) Lower Second Bicuspid (FDI: 45, Palmer: 5, Universal: 29)";
                break;
            case '(R) Lower First Bicuspid':
                description = "(R) Lower First Bicuspid (FDI: 44, Palmer: 4, Universal: 28)";
                break;
            case '(R) Lower Cuspid':
                description = "(R) Lower Cuspid (FDI: 43, Palmer: 3, Universal: 27)";
                break;
            case '(R) Lower Lateral Incisor':
                description = "(R) Lower Lateral Incisor (FDI: 42, Palmer: 2, Universal: 26)";
                break;
            case '(R) Lower Central Incisor':
                description = "(R) Lower Central incisor (FDI: 41, Palmer: 1, Universal: 25)";
                break;
            case '(L) Lower Central Incisor':
                description = "(L) Lower Central incisor (FDI: 31, Palmer: 1, Universal: 24)";
                break;
            case '(L) Lower Lateral Incisor':
                description = "(L) Lower Lateral Incisor (FDI: 32, Palmer: 2, Universal: 23)";
                break;
            case '(L) Lower Cuspid':
                description = "(L) Lower Cuspid (FDI: 33, Palmer: 3, Universal: 22)";
                break;
            case '(L) Lower First Bicuspid':
                description = "(L) Lower First Bicuspid (FDI: 34, Palmer: 4, Universal: 21)";
                break;
            case '(L) Lower Second Bicuspid':
                description = "(L) Lower Second Bicuspid (FDI: 35, Palmer: 5, Universal: 20)";
                break;
            case '(L) Lower First Molar':
                description = "(L) Lower First Molar (FDI: 36, Palmer: 6, Universal: 19)";
                break;
            case '(L) Lower Second Molar':
                description = "(L) Lower Second Molar (FDI: 37, Palmer: 7, Universal: 18)";
                break;
            case '(L) Lower Third Molar':
                description = "(L) Lower Third Molar (FDI: 38, Palmer: 8, Universal: 17)";
                break;

            default:
                description = "Unknown tooth.";
        }

        // Update tooltip content
        tooltip.innerHTML = description;

        // Set the tooltip to a fixed position in the upper-right corner
        tooltip.style.position = 'absolute';
        tooltip.style.right = '20px'; // Fixed position from the right edge
        tooltip.style.top = '20px'; // Fixed position from the top edge

        // Ensure the tooltip is shown
        tooltip.style.display = 'block';
    }








    //document.getElementById('teeth-container').appendChild(renderer.domElement);

    function render() {
        renderer.render(scene, camera);
    }
