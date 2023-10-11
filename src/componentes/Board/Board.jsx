import styles from './BoardStyle.module.css'
import Ships from '../Ships/Ships';
import shipsImg from '../../Imagens/ShipsImages/ShipsExport';
import { useState } from 'react';

function GameGride() {
  const [ships] = useState([
    {
      id: 0,
      sizeShip: 2,
      quantity: 2,
      content:<img src={shipsImg.ship1} className={styles.ships_img} id='ship_00'></img>
    },
    {
      id:1,
      sizeShip: 4,
      quantity: 1,
      content:<img src={shipsImg.ship2} className={styles.ships_img} id='ship_01'></img>
    },
    {
      id: 2,
      sizeShip: 1,
      quantity: 4,
      content:<img src={shipsImg.ship3} className={styles.ships_img} id='ship_02'></img>
    },
    {
      id: 3,
      sizeShip: 3,
      quantity: 1,
      content:<img src={shipsImg.ship4} className={styles.ships_img} id='ship_03'></img>
    },
    {
      id: 4,
      sizeShip: 3,
      quantity: 1,
      content:<img src={shipsImg.ship5} className={styles.ships_img} id='ship_04'></img>
    },
  ])

  const numRows = 10;
  const numCols = 10;

  const initialGridItems = Array.from({ length: numRows * numCols }, (_, index) => {
    return {
      id: index,
      content: null, // Conteúdo da célula, inicialmente nulo
    };
  });

  const [gridItems, setGridItems] = useState(initialGridItems);
  const [userOptShipsPosition, setUserOptShipsPosition] = useState([]);
  const [position, setPosition] = useState(0);
  const [ship, setShip] = useState(0);

  const setUserOptPosition = (ship, positionShip) => {
    const userOpt = { id: ship.id, position: []};
    let setUserOpt = [...userOptShipsPosition];
    for(let i=0; i<ship.sizeShip; i++) {
      userOpt.position[i] = positionShip+i;
    }
    setUserOpt.push(userOpt);
    setUserOptShipsPosition(setUserOpt);
  }

  const checkPosition = (positionShip, sizeShip) => {
    
    if (positionShip < 0 && positionShip > 99) {
      return false
    }

    let limitGrid = positionShip%10;
    if (limitGrid >= 6 && limitGrid <= 9) {
      limitGrid += sizeShip-1;
      if (limitGrid > 9) return false;
    }

    for (let i=0; i<sizeShip; i++) {
      for(let userOptPosition of userOptShipsPosition ) {
        if(userOptPosition.position.includes(positionShip+i) ) {
          return false;
        } 
      }
    }  
    return true;
  };

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

  const verifyCellRemoved = (positionShip, updatedGridItems) => {
    let cellsRemoved=0;
    
      for (let i=0; i < positionShip; i++) {
        if (updatedGridItems[i].id == positionShip) {
          break;
        }
        if(updatedGridItems[i].content != null) {
          cellsRemoved += updatedGridItems[i].sizeShip-1;
        }
      }
    return cellsRemoved
  }

  const shipsPosition = (shipNumber, positionShip) => {
    const updatedGridItems = [...gridItems];
    let shipInst = ships[shipNumber];
    console.log(checkPosition(positionShip, shipInst.sizeShip))
    if (checkPosition(positionShip, shipInst.sizeShip) && shipInst.quantity > 0) {
      ships.forEach(ship => {
        if(ship.id == shipNumber) {
          positionShip = positionShip - verifyCellRemoved(positionShip, updatedGridItems);
          
          setUserOptPosition(ship, positionShip + verifyCellRemoved(positionShip, updatedGridItems));

          if (ship.sizeShip > 1) {
            updatedGridItems.splice(positionShip+1, ship.sizeShip-1);
          }

          updatedGridItems[positionShip].content = ship.content;
          updatedGridItems[positionShip].sizeShip = ship.sizeShip;
          ship.quantity--;
        } 
      })
    } else {
      console.log ("Valor não disponivel");
    }
    
    setGridItems(updatedGridItems);
  };

  

  return (
    <div className={styles.board_container}>
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

      <div>
        <Ships/>
      </div>

      <div>
        <div>
          <label>Posição</label>
          <input type='number' onChange={(event) => Number(setPosition(event.target.value))}></input>
        </div>
        <div>
          <label>Navio</label>
          <input type='number' onChange={(event) => Number(setShip(event.target.value))}></input>
        </div>
        
        
        <button onClick={() => shipsPosition(ship, Number(position))}>Teste</button>
      </div>
    </div>
  );
}

export default GameGride;