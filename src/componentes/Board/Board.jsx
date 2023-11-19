import styles from './BoardStyle.module.css'
import Ships from '../Ships/Ships';
import shipsImg from '../../Imagens/ShipsImages/ShipsExport';
import { useState } from 'react';

function GameGride() {
  const [ships] = useState([
    {
      id: 0,
      name: "submarinos",
      size: 2,
      quantity: 4,
      content: <img src={shipsImg.ship1} className={styles.ships_img} id='ship_01'></img>
    },
    {
      id: 1,
      name: "contratorpedeiros",
      size: 3,
      quantity: 3,
      content: <img src={shipsImg.ship2} className={styles.ships_img} id='ship_02'></img>
    },
    {
      id: 2,
      name: "navios-tanque",
      size: 4,
      quantity: 2,
      content: <img src={shipsImg.ship3} className={styles.ships_img} id='ship_03'></img>
    },
    {
      id: 3,
      name: "porta-aviões",
      size: 5,
      quantity: 1,
      content: <img src={shipsImg.ship4} className={styles.ships_img} id='ship_04'></img>
    }
  ]);
  /*
  O grid e definido inicialmente de forma dinâmica, Iremos manipular ele em tempo real na tela. Os elementos são inicialmente nulos, a ideia se baseia em buscar o elemento de inserção da imagem do navio, sequente mente adaptar o grid ao tamanho correspondente do navio removendo e inserindo divs de forma dinâmica no grid.
  */
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
  const [position, setPosition] = useState('');
  const [column, setColumn] = useState('');
  const [row, setRow] = useState('');
  const [ship, setShip] = useState(0);
  const [remainingShips, setRemainingShips] = useState(ships.map(ship => ship.quantity));



  const handleColumnChange = (event) => {
    setColumn(event.target.value);
    updatePosition(event.target.value, row);
  };

  const handleRowChange = (event) => {
    setRow(event.target.value);
    updatePosition(column, event.target.value);
  };
  const updatePosition = (columnValue, rowValue) => {
    const newPosition = `${columnValue}${rowValue}`;
    setPosition(newPosition);
  };
  // Aqui definimos as posições que o usuario escolheu, que serão enviadas ao back.
  const setUserOptPosition = (ship, positionShip) => {
    const userOpt = { id: ship.id, position: [] };
    let setUserOpt = [...userOptShipsPosition];
    for (let i = 0; i < ship.sizeShip; i++) {
      userOpt.position[i] = positionShip + i;
    }
    setUserOpt.push(userOpt);
    setUserOptShipsPosition(setUserOpt);
  }

  //Checamos se a posição de inserção do navio está disponivel.
  const checkPosition = (positionShip, sizeShip) => {
    //se está dentro dos limites do gride.
    if (positionShip < 0 && positionShip > 99) {
      return false
    }

    //se está dentro do limite vertical do grid.
    let limitGrid = positionShip % 10;
    if (limitGrid >= 6 && limitGrid <= 9) {
      limitGrid += sizeShip - 1;
      if (limitGrid > 9) return false;
    }

    //se a posição já está ocupada por algum outro navio.
    for (let i = 0; i < sizeShip; i++) {
      for (let userOptPosition of userOptShipsPosition) {
        if (userOptPosition.position.includes(positionShip + i)) {
          return false;
        }
      }
    }
    return true;
  };

  //Defimos a classe responsavel por expandir a div do navio;
  const setClass = (sizeShip) => {

    if (sizeShip == 2) {
      return styles.ship_size2;
    } else if (sizeShip == 3) {
      return styles.ship_size3;
    } else if (sizeShip == 4) {
      return styles.ship_size4;
    } else if (sizeShip == 5) {
      return styles.ship_size5;
    }
  }

  /*
  Para fins de manter o posicionamento do grid correto, quando removemos células do grid verificamos quantas foram removidas e fazemos os ajustes necessários para não ocorrer erros no posicionamento do grid.
  */
  const verifyCellRemoved = (positionShip, updatedGridItems) => {
    let cellsRemoved = 0;
    for (let i = 0; i < positionShip; i++) {
      if (updatedGridItems[i].id == positionShip) {
        break;
      }
      if (updatedGridItems[i].content != null) {
        cellsRemoved += updatedGridItems[i].sizeShip - 1;
      }
    }
    return cellsRemoved;
  }

  const shipsPosition = (shipNumber, positionShip) => {
    const updatedGridItems = [...gridItems];
    let { size: shipSize, quantity: shipQuantity, content: shipContent } = ships[shipNumber];
    let cellsRemoved = verifyCellRemoved(positionShip, updatedGridItems);

    if (checkPosition(positionShip, shipSize) && shipQuantity > 0 && remainingShips[shipNumber] > 0) {
      // Diminuímos o número de células com base na posição do grid e dos navios já setados.
      positionShip = positionShip - cellsRemoved;

      // Nas posições do usuário, somamos as posições removidas novamente, pois não podemos ter alterações nas posições que vão para o back.
      setUserOptPosition(ships[shipNumber], positionShip + cellsRemoved);

      if (shipSize > 1) {
        updatedGridItems.splice(positionShip + 1, shipSize - 1);
      }

      updatedGridItems[positionShip].content = shipContent;
      updatedGridItems[positionShip].sizeShip = shipSize;

      // Atualize a quantidade restante de navios
      let updatedRemainingShips = [...remainingShips];
      updatedRemainingShips[shipNumber] = Math.max(0, updatedRemainingShips[shipNumber] - 1);
      setRemainingShips(updatedRemainingShips);

      setGridItems(updatedGridItems);
    } else {
      console.log("Valor não disponível ou limite de navios excedido");
    }
  };


  const letras10 = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
  const letras15 = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14];
  const num10 = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
  const num15 = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14];

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

      <div>
        <Ships />
      </div>

      <div>
        <div className={styles.container}>
          <label style={{ marginBottom: "5px", fontWeight: "bold" }}>Coluna</label>
          <input
            type='text'
            onChange={handleRowChange}
            style={{ padding: "8px", marginBottom: "10px", border: "1px solid #ccc", borderRadius: "4px" }}
          />

          <label style={{ marginBottom: "5px", fontWeight: "bold" }}>Linha</label>
          <input
            type='text'
            onChange={handleColumnChange}
            style={{ padding: "8px", marginBottom: "10px", border: "1px solid #ccc", borderRadius: "4px" }}
          />

          <label style={{ marginBottom: "5px", fontWeight: "bold" }}>Navio</label>
          <select
            value={ship}
            onChange={(event) => setShip(Number(event.target.value))}
            style={{ padding: "8px", marginBottom: "10px", border: "1px solid #ccc", borderRadius: "4px" }}
          >
            {[0, 1, 2, 3].map((value) => (
              <option key={value} value={value}>
                {value}
              </option>
            ))}
          </select>

          {remainingShips.map((quantity, index) => (
            <label key={index} style={{ marginBottom: "5px" }}>{`Quantidade do navio ${index}: ${quantity}`}</label>
          ))}

          <button
            className={styles.btn}
            onClick={() => shipsPosition(ship, Number(position))}
          >
            Adicionar navio
          </button>
          {/* Concluído button */}
          <button
            className={styles.con}
            onClick={() => handleConcluidoClick()}
          >
            Concluído
          </button>
        </div>

      </div>



    </div>
  );

}


export default GameGride;