var shipLeft = 0;
var shipTop = 350;
var shipSize = 50;
var baseHeight = 50;
var baseWidth = 50;
var baseLeft = 50;
var baseTop = 280;
var INC = 5;
var stopGame = false;
var missiles = [];
var enemy = document.getElementById('alien');
var enemyCharge = 0;

function finishGame()
{
    stopGame = true;
}

function anim(e) {
    var ship = document.getElementById("ship");
    var container = document.getElementById("field");

    ship.style.top=shipTop+'px';

    if (e.keyCode === 39) {
        e.preventDefault();
        shipLeft += INC;
        ship.style.left = shipLeft + "px";
        if (shipLeft >= 450)
            shipLeft -= INC;
    }
    if (e.keyCode === 37) {
        e.preventDefault();
        shipLeft -= INC;
        ship.style.left = shipLeft + "px";
        if (shipLeft <= 0)
            shipLeft += INC;
    }
    if (e.keyCode === 32) {
        e.preventDefault();
        // Spacebar (fire)
        missiles.push({
            left: shipLeft + 20,
            top: shipTop - 20,
            dir: true 
        });
        drawMissiles();
    }
}

document.onkeydown = anim;

var movingRight = true;
var movingDown = false;
var timerObject = null;

function parseStylePx(stylePX) {
    // remove the 'px' from the end of the string;
    stylePX = stylePX.substr(0, stylePX.length - 2);
    return parseInt(stylePX);
}

function moveEnemy() {
    let div = document.getElementById("alien");

    let x = parseStylePx(div.style.left);
    let y = parseStylePx(div.style.top);

    let divWidth = parseStylePx(div.style.width);
    let divHeight = parseStylePx(div.style.height);

    if (movingRight) {
        if ((x + INC + divWidth) > 500) {
            movingRight = false;
            movingDown = true;
            x -= INC;
        } else {
            x += INC;
        }
    } else {
        if ((x - INC) < 0) {
            movingRight = true;
            movingDown = true;

            x += INC;
        } else {
            x -= INC;
        }
    }

    if (movingDown) {
        if ((y + INC + divHeight) > 300) {
            movingDown = false;
            alert("Game over. You loose.");
            return;
        } else {
            y += 4 * INC;
            movingDown = false;
        }
    }

    // Enemy fire
    enemyCharge++;
    if (enemyCharge > 20) {
        enemyCharge = 0;
        missiles.push({
            left: x + 20,
            top: y + 40,
            dir: false
        });
        drawMissiles();
    }

    div.style["left"] = x + "px";
    div.style["top"] = y + "px";
}
function drawMissiles() {
    document.getElementById('missiles').innerHTML = "";
    for(var i = 0 ; i < missiles.length ; i++ ) {
        document.getElementById('missiles').innerHTML += `<div class='missile' style='left:${missiles[i].left}px; top:${missiles[i].top}px'></div>`;
    }
}
function moveMissiles() {
    for(var i = 0 ; i < missiles.length ; i++ ) {
        let delta = missiles[i].dir ? -8 : 8;
        missiles[i].top = missiles[i].top + delta;
    }
}

function collisionDetection() {
    let enemy = document.getElementById("alien");
    let enemyLeft = parseStylePx(enemy.style.left);
    let enemyTop = parseStylePx(enemy.style.top);
    let enemyWidth = parseStylePx(enemy.style.width);
    let enemyHeight = parseStylePx(enemy.style.height);

    let base = document.getElementById("base");
    for (var missile = 0; missile < missiles.length; missile++) {
         if (
            // Missile towards enemy
            missiles[missile].dir == true &&
            missiles[missile].left >= enemyLeft  &&
            missiles[missile].left <= (enemyLeft + enemyWidth)  &&
            missiles[missile].top <= (enemyTop + enemyHeight)  &&
            missiles[missile].top >= enemyTop
        ) {
            alert("Win!!!!");
            finishGame();
            return;
        } else if (
            // Missile towards hero
            missiles[missile].dir == false &&
            missiles[missile].left >= shipLeft  &&
            missiles[missile].left <= (shipLeft + shipSize)  &&
            missiles[missile].top <= (shipTop + shipSize)  &&
            missiles[missile].top >= shipTop
        ) {
            alert("You loose!!!!");
            finishGame();
            return;
        } else if (base) {
            let baseLeft = parseStylePx(base.style.left);
            let baseTop = parseStylePx(base.style.top);
            let baseWidth = parseStylePx(base.style.width);
            let baseHeight = parseStylePx(base.style.height);
            if (
                // Missile towards base
                missiles[missile].left >= baseLeft  &&
                missiles[missile].left <= (baseLeft + baseWidth)  &&
                missiles[missile].top <= (baseTop + baseHeight)  &&
                missiles[missile].top >= baseTop
            ) {
                base.parentElement.removeChild(base);
                missiles.splice(missile, 1);
            }
        }
    }
}

function gameLoop() {
    if (stopGame)
    {
        stopGame = false;
        return;
    }
    setTimeout(gameLoop, 100)
    moveMissiles();
    drawMissiles();
    moveEnemy();
    collisionDetection();
}

function gameStart() {
    let base = document.getElementById("base");
    base.style.top = baseTop + 'px';
    base.style.left = baseLeft + 'px';
    base.style.width = baseWidth + 'px';
    base.style.height = baseHeight + 'px';
    gameLoop();
}