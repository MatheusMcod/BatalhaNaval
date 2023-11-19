import styles from '../Screen/ScreenBoard.module.css'
import Modal from '../modal/modal';
import { useState } from 'react';

function GameGride() {
    const [isModalOpen, setIsModalOpen] = useState(false);


    const numRows = 10;
    const numCols = 10;

    const initialGridItems = Array.from({ length: numRows * numCols }, (_, index) => {
        return {
            id: index,
            content: null, // Conteúdo da célula, inicialmente nulo.
        };
    });

    const [gridItems, setGridItems] = useState(initialGridItems);
    const [userOptShipsPosition, setUserOptShipsPosition] = useState([]);
    const [position, setPosition] = useState(0);
    const [ship, setShip] = useState(0);


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


                        {gridItems.map(item => {
                            if (item.content == null) {
                                return (
                                    <div key={item.id} className={styles.grid_item} id={`item-${item.id}`}>
                                        {item.content}
                                    </div>
                                )
                            } else {
                                return (
                                    <div key={item.id} className={`${styles.grid_item} ${setClass(item.sizeShip)}`} id={`item-${item.id}`}>
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


                        {gridItems.map(item => {
                            if (item.content == null) {
                                return (
                                    <div key={item.id} className={styles.grid_item} id={`item-${item.id}`}>
                                        {item.content}
                                    </div>
                                )
                            } else {
                                return (
                                    <div key={item.id} className={`${styles.grid_item} ${setClass(item.sizeShip)}`} id={`item-${item.id}`}>
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
                <button className={styles.button} onClick={() => setIsModalOpen(true)}>
                    Abrir Modal
                </button>
                <Modal isOpen={isModalOpen} width={400} height={400} setIsModalClose={() => setIsModalOpen(!isModalOpen)}>
                {/* seu conteudo */}




                </Modal>

            </div>

        </>

    );
}

export default GameGride;