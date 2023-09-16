import styles from './BodyHomeStyle.module.css'
import {Link} from "react-router-dom"

function Body(){
    return(
        <div className={styles.body}>
            <ul>
                <li className={styles.buttonsList}>
                    <Link to="/difficulty">
                         <button className={styles.buttons}>CONTRA IA</button>
                    </Link>
                   <button className={styles.buttons}><a href='#'>2 JOGADORES</a></button>
                   <button className={styles.buttons}><a href='#'>CONFIGURAÇÕES</a></button>
                </li>
            </ul>
        </div>
    );
}

export default Body;