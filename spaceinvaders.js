// Game settings
var ALIEN_HSPEED = 5;
var ALIEN_VSPEED = 5;
var ALIEN_WIDTH = 40;
var ALIEN_HEIGHT = 35;
var ALIEN_CNT_X = 6;
var ALIEN_CNT_Y = 4;
var ALIEN_DIST = 10;
var BOARD_WIDTH = 600;
var BOARD_HEIGHT = 400;
var SHIP_WIDTH = 50;
var SHIP_HEIGHT = 50;
var SHIP_HSPEED = 10;
var BASE_WIDTH = 50;
var BASE_HEIGHT = 50;
var BASE_OFFSET = BOARD_HEIGHT - SHIP_HEIGHT - BASE_HEIGHT - 20;
var MISSILE_WIDTH = 5;
var MISSILE_HEIGHT = 10;
var MISSILE_SPEED = 20;

// Game object
var game  = null;

function createGame() {
    //try {
        let game = {
            board: {
                svgElmt : null,
                height: BOARD_HEIGHT,
                width: BOARD_WIDTH
            },
            ship: {
                svgElmt : null,
                width: SHIP_WIDTH,
                height: SHIP_HEIGHT,
                x: 0,
                y: 0
            },
            bases: [],
            aliens: [],
            missiles: [],
            dir: ALIEN_HSPEED,

            initGame: function() {
                this.board.svgElmt = document.getElementById("svgBoard");
                this.ship.svgElmt = document.getElementById("ship");
                this.ship.x = this.board.width/2 - this.ship.width/2;
                this.ship.y = this.board.height - this.ship.height;

                for (i=0; i<3; i++) {
                    this.bases[i] = {
                        svgElmt: document.getElementById("base" + i),
                        width: BASE_WIDTH,
                        height: BASE_HEIGHT,
                        x: this.board.width/4 * (i+1),
                        y: BASE_OFFSET
                    }
                    // Render
                    this.bases[i].svgElmt.setAttribute("width", this.bases[i].width + "");
                    this.bases[i].svgElmt.setAttribute("height", this.bases[i].height + "");
                    this.bases[i].svgElmt.setAttribute("y", this.bases[i].y + "");
                    this.bases[i].svgElmt.setAttribute("x", this.bases[i].x + "");
                }

                for (i=0; i<ALIEN_CNT_X; i++) {
                    for (y=0; y<ALIEN_CNT_Y; y++) {
                        if (typeof this.aliens[i] === "undefined" ) {
                            this.aliens[i] = [];
                        }

                        this.aliens[i][y] = {
                            svgElmt: document.getElementById("alien" + i + "" + y),
                            width: ALIEN_WIDTH,
                            height: ALIEN_HEIGHT,
                            x: this.board.width/(ALIEN_CNT_X+1) * (i+1),
                            y: (ALIEN_HEIGHT+ALIEN_DIST)*y,
                        }
                        // Render
                        this.aliens[i][y].svgElmt.setAttribute("width", this.aliens[i][y].width + "");
                        this.aliens[i][y].svgElmt.setAttribute("height", this.aliens[i][y].height + "");
                        this.aliens[i][y].svgElmt.setAttribute("y", this.aliens[i][y].y + "");
                        this.aliens[i][y].svgElmt.setAttribute("x", this.aliens[i][y].x + "");
                    }
                   
                }

                //Set handler
                let self = this;
                document.onkeydown = function (evt) {
                    self.keyHandler(evt);
                };

                //Initial render
                this.board.svgElmt.setAttribute("width", this.board.width + "");
                this.board.svgElmt.setAttribute("height", this.board.height + "");
                this.ship.svgElmt.setAttribute("width", this.ship.width + "");
                this.ship.svgElmt.setAttribute("height", this.ship.height + "");
                this.ship.svgElmt.setAttribute("y", this.ship.y + "");
            },

            gameLoop: function() {
                let self = this;
                this.moveMissiles();
                if (!this.moveAliens()) {
                    this.finishGame();
                    return;
                }
                if (!this.detectCollisions()) {
                    this.finishGame();
                    return;
                }
                this.render();

                setTimeout(function() {
                    self.gameLoop();
                }, 100);
            },

            getAliensDims() {
                let minX = -1;
                let maxX = 0;
                let minY = -1;
                let maxY = 0;

                for (i=0; i<ALIEN_CNT_X; i++) {
                    for (y=0; y<ALIEN_CNT_Y; y++) {
                        if (typeof this.aliens[i][y] === "undefined") {
                            continue;
                        }
                        if (minX == -1) {
                            minX = this.aliens[i][y].x;
                        }
                        if (minY == -1) {
                            minY = this.aliens[i][y].y;
                        }
                        maxX = (this.aliens[i][y].x > maxX) ? this.aliens[i][y].x : maxX;
                        minX = (this.aliens[i][y].x < minX) ? this.aliens[i][y].x : minX;
                        maxY = (this.aliens[i][y].y > maxY) ? this.aliens[i][y].y : maxY;
                        minY = (this.aliens[i][y].y < minY) ? this.aliens[i][y].y : minY;
                        
                    }
                }
                return {
                    minX: minX,
                    maxX: maxX,
                    minY: minY,
                    maxY: maxY
                }
            },

            moveAliens: function (){
                let dim = this.getAliensDims();
                let moveDown = false;
                if (dim.minX <= 0) {
                    this.dir = ALIEN_HSPEED;
                    moveDown = true;

                }
                else if (dim.maxX > this.board.width - ALIEN_WIDTH) {
                    this.dir = -ALIEN_HSPEED;
                    moveDown = true;
                }
                if (dim.maxY >= BASE_OFFSET - ALIEN_HEIGHT) {
                    alert("Game over");
                    return false;
                }
                for (i=0; i<ALIEN_CNT_X; i++) {
                    for (y=0; y<ALIEN_CNT_Y; y++) {
                        if (typeof this.aliens[i][y] === "undefined") {
                            continue;
                        }
                        this.aliens[i][y].x += this.dir;
                        if (moveDown) {
                            this.aliens[i][y].y += ALIEN_VSPEED;
                        }

                        // FIRE
                        if (Math.random() > 0.995) {
                            let missileX = this.aliens[i][y].x + ALIEN_WIDTH/2;
                            let missileY = this.aliens[i][y].y;
                            let svgMissile = this.createMissileSVG(missileX, missileY, "blue");
        
                            this.board.svgElmt.appendChild(svgMissile);
        
                            this.missiles.push({
                                x: missileX,
                                y: missileY,
                                svgElmt: svgMissile,
                                dir: MISSILE_SPEED
                            });   
                        }                     
                        
                    }
                }
                return true;

            },

            createMissileSVG: function(x,y, color) {
                let xmlns = "http://www.w3.org/2000/svg";
                let elem = document.createElementNS(xmlns, "rect");
 
                elem.setAttributeNS(null,"x",x);
                elem.setAttributeNS(null,"y",y);
                elem.setAttributeNS(null,"width",MISSILE_WIDTH);
                elem.setAttributeNS(null,"height",MISSILE_HEIGHT);
                elem.setAttributeNS(null,"fill", color);
                return elem;
            },

            moveMissiles: function() {
                for (var i = 0 ; i < this.missiles.length ; i++ ) {
                    this.missiles[i].y = this.missiles[i].y + this.missiles[i].dir;
                }
            },

            detectCollisions: function() {
                missileLoop:
                for (var i = 0 ; i < this.missiles.length ; i++ ) {
                    if (this.missiles[i].dir > 0) {
                        //Alien missiles     
                        if (this.missiles[i].y > this.board.height) {
                            // missile missed
                            this.missiles[i].svgElmt.remove();
                            this.missiles.splice(i, 1);
                            continue missileLoop;
                        } else if (
                            this.missiles[i].y >= this.ship.y &&
                            this.missiles[i].y <= this.ship.y + this.ship.height &&
                            this.missiles[i].x >= this.ship.x &&
                            this.missiles[i].x <= this.ship.x + this.ship.width 
                        ) {
                            this.missiles[i].svgElmt.remove();
                            this.missiles.splice(i, 1);
                            alert("You loose!!!!");
                            return false;
                        }
                    } else {
                        // Ship missiles
                        let aliensCnt = 0;
                        for (j=0; j<ALIEN_CNT_X; j++) {
                            for (k=0; k<ALIEN_CNT_Y; k++) {
                                if (typeof this.aliens[j][k] === "undefined") {
                                    continue;
                                }
                                if (
                                    this.missiles[i].y >= this.aliens[j][k].y &&
                                    this.missiles[i].y <= this.aliens[j][k].y + this.aliens[j][k].height &&
                                    this.missiles[i].x >= this.aliens[j][k].x &&
                                    this.missiles[i].x <= this.aliens[j][k].x + this.aliens[j][k].width 
                                ) {
                                    this.missiles[i].svgElmt.remove();
                                    this.missiles.splice(i, 1);
                                    this.alienDestroy(j, k);
                                    continue missileLoop;
                                } else {
                                    aliensCnt++;
                                }      
                            }
                        }
                        if (aliensCnt == 0) {
                            alert("You won!");
                            return false;
                        }
                    }
                    for (var j = 0; j < this.bases.length; j++) {
                        console.log(this.missiles[i])
                        if (
                            this.missiles[i].y >= this.bases[j].y &&
                            this.missiles[i].y <= this.bases[j].y + this.bases[j].height &&
                            this.missiles[i].x >= this.bases[j].x &&
                            this.missiles[i].x <= this.bases[j].x + this.bases[j].width 
                        ) {
                            this.missiles[i].svgElmt.remove();
                            this.missiles.splice(i, 1);
                            this.baseDestroy(j);
                            continue missileLoop;
                        }

                    }
                }
                return true;
            },

            baseDestroy: function(j) {
                this.bases[j].svgElmt.remove();
                this.bases.splice(j, 1);
            },
            
            alienDestroy: function(i, y) {
                this.aliens[i][y].svgElmt.remove();
                this.aliens[i].splice(y, 1);
            },

            render: function() {
                this.ship.svgElmt.setAttribute("x", this.ship.x + "");
                this.renderAliens();
                this.renderMissiles();
            },

            renderAliens: function() {
                for (i=0; i<ALIEN_CNT_X; i++) {
                    for (y=0; y<ALIEN_CNT_Y; y++) {
                        if (typeof this.aliens[i][y] === "undefined") {
                            continue;
                        }
                        this.aliens[i][y].svgElmt.setAttribute("x", this.aliens[i][y].x + "");
                        this.aliens[i][y].svgElmt.setAttribute("y", this.aliens[i][y].y + "");
                        
                    }
                }
            },

            renderMissiles: function() {
                for(var i = 0 ; i < this.missiles.length ; i++ ) {
                   this.missiles[i].svgElmt.setAttribute("x", this.missiles[i].x + "");
                   this.missiles[i].svgElmt.setAttribute("y", this.missiles[i].y + "");
                }
            },

            keyHandler: function(evt) {
                if (evt.keyCode === 39) {
                    evt.preventDefault();
                    this.ship.x += SHIP_HSPEED;
                    if (this.ship.x > this.board.width - this.ship.width) {
                        this.ship.x = this.board.width - this.ship.width;
                        return;
                    }
                }
                if (evt.keyCode === 37) {
                    evt.preventDefault();
                    this.ship.x -= SHIP_HSPEED;
                    if (this.ship.x < 0) {
                        this.ship.x = 0;
                        return;
                    }
                }
                if (evt.keyCode === 32) {
                    evt.preventDefault();

                    let x = this.ship.x + this.ship.width/2;
                    let y = this.ship.y;
                    let svgMissile = this.createMissileSVG(x, y, "yellow");

                    this.board.svgElmt.appendChild(svgMissile);

                    this.missiles.push({
                        x: x,
                        y: y,
                        svgElmt: svgMissile,
                        dir: -MISSILE_SPEED
                    });
                }
            }
        }
        return game;
    //} catch(ex) {
    //    alert(ex);
    //}
    return null;
}

function gameStart() {
    game = createGame();
    game.initGame();
    game.gameLoop();


}