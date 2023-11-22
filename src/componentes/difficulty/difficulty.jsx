import style from './difficultyStyle.module.css'
import { Link } from 'react-router-dom';
function Wdifficulty() {
    return (
        <>
            <div className={style.back}>

            </div>
            <div className={style.balls}>
                <Link to={"/Board"}>
                    <button className={`${style.ball} `}><h2>FÁCIL</h2></button>
                </Link>

                <Link to={"/Board"}>
                    <button className={`${style.ball} `}><h2>Médio</h2></button>
                </Link>

                <Link to={"/Board"}>
                    <button className={`${style.ball} `}><h2>Difícil</h2></button>
                </Link>
            </div>
        </>
    );
}

export default Wdifficulty;