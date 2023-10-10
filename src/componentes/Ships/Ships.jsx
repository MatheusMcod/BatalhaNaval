//import style from './ShipsStyle.module.css';
import ships from '../../Imagens/ShipsImages/ShipsExport';
import style from './ShipsStyle.module.css';


function Ships() {

    return(
        <div className={style.grid_container_ships}>
            <img src={ships.ship2} rel='ancora' className={style.img_ship4}></img>
            <img src={ships.ship4} rel='ancora' className={style.img_ship3}></img>
            <img src={ships.ship5} rel='ancora' className={style.img_ship3}></img>
            <img src={ships.ship5} rel='ancora' className={style.img_ship3}></img>
            <img src={ships.ship1} rel='ancora' className={style.img_ship2}></img>
            <img src={ships.ship1} rel='ancora' className={style.img_ship2}></img>
            <img src={ships.ship3} rel='ancora' className={style.img_ship1}></img>
            <img src={ships.ship3} rel='ancora' className={style.img_ship1}></img>
            <img src={ships.ship3} rel='ancora' className={style.img_ship1}></img>
            <img src={ships.ship3} rel='ancora' className={style.img_ship1}></img>
            <div className={style.container_btn}>
                
            </div>
        </div>
    );
}

export default Ships;