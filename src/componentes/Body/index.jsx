import './styleBody.css'

function Body(){
    return(
        <div className='body'>
            <ul className='ListaDeBotoes'>
                <li>
                   <button><a href=''>CONTRA IA</a></button>
                   <button><a href=''>2 JOGADORES</a></button>
                   <button><a href=''>CONFIGURAÇÕES</a></button>
                </li>
            </ul>
        </div>
    );
}

export default Body();