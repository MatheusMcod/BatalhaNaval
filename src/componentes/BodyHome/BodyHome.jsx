import styles from './BodyHomeStyle.module.css'

function Body(){
    return(
        <div className={styles.body}>
            <ul>
                <li className={styles.buttonsList}>
                   <button className={styles.buttons}><a href='#'>CONTRA IA</a></button>
                   <button className={styles.buttons}><a href='#'>2 JOGADORES</a></button>
                   <button className={styles.buttons}><a href='#'>CONFIGURAÇÕES</a></button>
                </li>
            </ul>
        </div>
    );
}

export default Body;