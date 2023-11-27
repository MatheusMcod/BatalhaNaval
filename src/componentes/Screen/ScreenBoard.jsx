import styles from '../Screen/ScreenBoard.module.css'
import Modal from '../modal/modal';
import { useState, useEffect } from 'react';
import axios from 'axios';
import shipsImg from '../../Imagens/ShipsImages/ShipsExport';

function GameGride() {
    


    const numRows = 10;
    const numCols = 10;

    const gridItensUser = Array.from({ length: numRows * numCols }, (_, index) => {
        return {
            id: index,
            content: null,
            hit: null
        };
    });

    const gridItensBot = Array.from({ length: numRows * numCols }, (_, index) => {
        return {
            id: index,
            content: null,
            hit: null
        };
    });

    const [images] = useState([
        {
          name: "Submarinos",
          content: <img src={shipsImg.ship1} className={styles.ships_img}></img>
        },
        {
          name: "Contratorpedeiro",
          content: <img src={shipsImg.ship2} className={styles.ships_img}></img>
        },
        {
          name: "Navio_Tanque",
          content: <img src={shipsImg.ship3} className={styles.ships_img}></img>
        },
        {
          name: "PortaAvioes",
          content: <img src={shipsImg.ship4} className={styles.ships_img}></img>
        }
    ]);

    const [dadosModal, setDadosModal] = useState([]);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [gridUser, setGridUser] = useState(gridItensUser);
    const [gridBot, setGridBot] = useState(gridItensBot);
    const [botShips, setBotShips] = useState([]);
    const [userShips, setUserShips] = useState([]);
    const [numberShot, setNumberShot] = useState(0);
    const [shotType, setShotType] = useState('normal');
    const [quantEspecial, setquantEspecial] = useState(2);
    const [sendMovBack, setMovBack] = useState({shotType: 'normal', move: []});


    //Defimos a classe responsavel por expandir a div do navio;
    const setClass = (sizeShip) => {
        if (sizeShip == 1) {
            return styles.ship_size1;
        } else if (sizeShip == 2) {
            return styles.ship_size2;
        } else if (sizeShip == 3) {
            return styles.ship_size3;
        } else if (sizeShip == 4) {
            return styles.ship_size4;
        }
    }

    const letras10 = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
    const letras15 = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'k', 'l', 'm', 'n', 'o'];
    const num10 = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
    const num15 = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15];

    let letras;
    if (numRows === 10) {
        letras = letras10;
    } else if (numRows === 15) {
        letras = letras15;
    }

    let num;
    if (numCols === 10) {
        num = num10;
    } else if (numCols === 15) {
        num = num15;
    }

    const fetchBotShips = async () => {
        try {
          const response = await axios.get("http://batalhanaval/bot/allships");
          let image = [...images];
          if (response.status == 200) {
            let shipsBot = response.data;
            shipsBot.forEach(ship => {
                let found = image.find(image => ship.name == image.name);
                if (found != undefined) {
                    ship.image = found.content;
                }
            });
            setBotShips(shipsBot);
          } else {
            throw new Error('Erro na requisição');
          }
    
        } catch (error) {
            console.error('Falha ao enviar os dados:', error);
        }
    };

    const fetchUserShips = async () => {
        try {
          const response = await axios.get("http://batalhanaval/user/allships");
          let image = [...images];

          if (response.status == 200) {
            let shipsUser = response.data;
            shipsUser.forEach(ship => {
                let found = image.find(image => ship.name == image.name);
                if (found != undefined) {
                    ship.image = found.content;
                }
            });
            setUserShips(shipsUser);
          } else {
            throw new Error('Erro na requisição');
          }
    
        } catch (error) {
            console.error('Falha ao enviar os dados:', error);
        }
    };

    const getBotMov = async () => {
        if (numberShot == 3) {
            try {
            const response = await axios.get("http://batalhanaval/bot/move?gridSize=99&difficulty=easy");

            if (response.status == 200) {
                let grid = [...gridUser];
                let data = response.data;
                data.forEach(item => {
                    grid[item.move].hit = item.target;
                });
                setGridUser(grid);
            } else {
                throw new Error('Erro na requisição');
            }
        
            } catch (error) {
                console.error('Falha ao enviar os dados:', error);
            }
        }    
    };

    const definePositionsShips = () => {
        let shipsBot = [...botShips];
        let shipsUser = [...userShips];
        let positionsBot = [...gridBot];
        let positionsUser = [...gridUser];
        
        shipsBot.forEach((shipBot) => {
            positionsBot[shipBot.position].content = shipBot.shipID;
            
        });
        
        shipsUser.forEach((shipUser) => {
            positionsUser[shipUser.position].content = shipUser.shipID;
        });

        setGridBot(positionsBot);
        setGridUser(positionsUser);
    }

    const especialMov = (divId, shipId) => {
        let quantShot = numberShot;
        let botGrid = [...gridBot];
        const adjacentPositions = [0, -11, -10, -9, -1, 1, 9, 10, 11];
        sendMovBack.shotType = 'especial';
        let id = divId;

        for (let adjacent of adjacentPositions) {
            divId = id + adjacent;
            
            if (divId > 99 || divId < 0) {
                continue;
            }
            if (id % 10 == 0 && (adjacent == -11 || adjacent == -1 || adjacent == 9)) {
                continue;
            }
            if (id % 10 == 9 && (adjacent == 11 || adjacent == 1 || adjacent == -9)) {
                continue; 
            }

            if(shipId.trim() && botGrid[divId].hit == null) {
                botGrid[divId].hit = 'hit';

                let shipsBot = [...botShips];
                if (shipsBot.filter(ship => ship.shipID == shipId).length == 1) {
                    shipsBot.forEach(ship => {
                        if(ship.shipID == shipId) {
                            ship.shipSize = 0;
                        }
                    });
                    setBotShips(shipsBot);
                    
                } else {
                    shipsBot = shipsBot.filter(ship => ship.position != divId);
                    setBotShips(shipsBot);
                }
                sendMovBack.move.push(divId);
            } else if (!shipId.trim() && botGrid[divId].hit == null) {
                botGrid[divId].hit = 'miss';
                quantShot += 1;
                setNumberShot(quantShot);
                sendMovBack.move.push(divId);
            }
        }

        setNumberShot(3);
        setquantEspecial(quantEspecial - 1);
        setShotType('normal');
    }

    const handleClick = (event) => {
        const divId = event.currentTarget.id;
        const shipId = event.currentTarget.textContent;
        let quantShot = numberShot;

        let gridSearchId = divId.match(/\d+/);
        gridSearchId = gridSearchId ? parseInt(gridSearchId[0]) : null;

        let botGrid = [...gridBot];

        if (shotType == 'normal') {
            if(shipId.trim() && botGrid[gridSearchId].hit == null && quantShot != 3) {
                botGrid[gridSearchId].hit = 'hit';
                quantShot += 1;
                setNumberShot(quantShot);

                let shipsBot = [...botShips];
                if (shipsBot.filter(ship => ship.shipID == shipId).length == 1) {
                    shipsBot.forEach(ship => {
                        if(ship.shipID == shipId) {
                            ship.shipSize = 0;
                        }
                    });
                    setBotShips(shipsBot);
                    
                } else {
                    shipsBot = shipsBot.filter(ship => ship.position != gridSearchId);
                    setBotShips(shipsBot);
                }
                sendMovBack.move.push(gridSearchId);
            } else if (!shipId.trim() && botGrid[gridSearchId].hit == null && quantShot != 3) {
                botGrid[gridSearchId].hit = 'miss';
                quantShot += 1;
                setNumberShot(quantShot);
                sendMovBack.move.push(gridSearchId);
            }
        } else {
            especialMov(gridSearchId, shipId);
        }
        
    
        setGridBot(botGrid);
      };

    const sendMove = async () => {
        if (numberShot == 3) {
            try {
                const response = await axios.post('http://batalhanaval/user/move', sendMovBack);
                if (response.status == 200) {
                    setNumberShot(0);
                    setMovBack({shotType: 'normal', move: []});
                    const verify = await axios.get('http://batalhanaval/verify/end');
                    console.log(verify)
                    if (verify.data != false) {
                        setIsModalOpen(true);
                        const date = await axios.get('http://batalhanaval/get/logs');
                        console.log(date)
                        setDadosModal(date);
                    }
                } else {
                    throw new Error('Erro na requisição');
                }
            
                } catch (error) {
                    console.error('Falha ao enviar os dados:', error);
            } 
        }
    };

    useEffect(() => {
        fetchBotShips();
        fetchUserShips();
      }, []);


    return (
        <>
            <div className={styles.board_container}>

                <div className={styles.sub_container}>
                    <div className={styles.DivRow}>
                        <ul className={styles.listahorizontal}>
                            {letras.map((letra, index) => (
                                <li key={index}>{letra}</li>
                            ))}
                        </ul>
                    </div>
                    <div className={styles.DivCollun}>
                        <ul className={styles.listaVertical}>
                            {num.map((numero, index) => (
                                <li key={index}>{numero}</li>
                            ))}
                        </ul>
                    </div>
                    <div className={styles.grid_container_board}>

                        {gridUser.map(item => {
                            if (item.hit != null) {
                                return (
                                    <div key={item.id} className={`${styles.grid_item} ${item.hit == 'hit' ? styles.shot_hit : styles.shot_miss}`} id={`item-${item.id}`}>
                                        {item.content}
                                    </div>
                                )
                            } else {
                                return (
                                    <div key={item.id} className={styles.grid_item} id={`item-${item.id}`}>
                                        {item.content}
                                    </div>
                                )
                            }    
                        })}
                    </div>
                </div>
                <div className={styles.sub_container}>
                    <div className={styles.DivRow}>
                        <ul className={styles.listahorizontal}>
                            {letras.map((letra, index) => (
                                <li key={index}>{letra}</li>
                            ))}
                        </ul>
                    </div>
                    <div className={styles.DivCollun}>
                        <ul className={styles.listaVertical}>
                            {num.map((numero, index) => (
                                <li key={index}>{numero}</li>
                            ))}
                        </ul>
                    </div>
                    <div className={styles.grid_container_board}>
                        {gridBot.map(item => {
                            if (item.hit != null) {
                                return (
                                    <div key={item.id} className={`${styles.grid_item} ${item.hit == 'hit' ? styles.shot_hit : styles.shot_miss}`} id={`item-${item.id}`} onClick={handleClick}>
                                        {item.content}
                                    </div>
                                )
                            } else {
                                return (
                                    <div key={item.id} className={styles.grid_item} id={`item-${item.id}`} onClick={handleClick}>
                                        {item.content}
                                    </div>
                                )
                            }    
                        })}
                    </div>
                </div>


            </div>



            {/* modal */}
            <div className={styles.modal}>
                <button className={styles.button} onClick={() => definePositionsShips()/*setIsModalOpen(true)*/}>
                    Abrir Modal
                </button>
                <button className={styles.button} onClick={() => {sendMove(); getBotMov();}}>
                    Movimento
                </button>
                <button className={styles.button} onClick={() => {setShotType('especial')}}>
                    Especial
                </button>
                <Modal isOpen={isModalOpen} width={400} height={400} setIsModalClose={() => setIsModalOpen(!isModalOpen)}>
                {/* seu conteudo */}
                    <ul>
                        {dadosModal.map((item, index) => (
                            <li key={index}>{item}</li>
                        ))}
                    </ul> 
                </Modal>

            </div>

        </>

    );
}

export default GameGride;