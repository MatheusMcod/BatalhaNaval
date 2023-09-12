import './style.css'
import ancora from '../../Imagens/ancora.png'

function Header(){

    return(

        <header>
          <h1>BATALHA NAVAL</h1>
          <img className="icone" src={ancora}  rel='ancora'/>
        </header>
    );

}

export default Header();