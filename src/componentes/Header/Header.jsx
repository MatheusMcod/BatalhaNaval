import styles from './HeaderStyle.module.css'
import ancora from '../../Imagens/ancora.png'

function Header(){

    return(
        <header className={styles.header}>
          <h1 className={styles.title}>BATALHA NAVAL</h1>
          <img className={styles.icone} src={ancora} rel='ancora'/>
        </header>
    );

}

export default Header;