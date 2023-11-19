import ships from '../../Imagens/ShipsImages/ShipsExport';
import style from './ShipsStyle.module.css';


function Ships() {

    return (
        <div className={style.grid_container_ships}>

            <img src={ships.ship1} rel='submarino' className={style.img_ship2}></img>
            <img src={ships.ship1} rel='submarino' className={style.img_ship2}></img>
            <img src={ships.ship1} rel='submarino' className={style.img_ship2}></img>
            <img src={ships.ship1} rel='submarino' className={style.img_ship2}></img>
            <img src={ships.ship2} rel='contratorpedeiros' className={style.img_ship3}></img>
            <img src={ships.ship2} rel='contratorpedeiros' className={style.img_ship3}></img>
            <img src={ships.ship2} rel='contratorpedeiros' className={style.img_ship3}></img>
            <img src={ships.ship3} rel='navios-tanque' className={style.img_ship4}></img>
            <img src={ships.ship3} rel='navios-tanque' className={style.img_ship4_2}></img>
            <img src={ships.ship4} rel='porta-aviões' className={style.img_ship5}></img>

            <div className={style.container_btn}>

            </div>
        </div>
    );
}

export default Ships;