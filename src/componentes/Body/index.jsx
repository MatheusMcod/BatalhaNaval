import './styleBody.css'

const bt = ["Contra IA", "2 jogadores", "Configurações" ];

function Body(){
    return(
        <div className='body'>
            <ul className='ListaDeBotoes'>
                <li>
                    {bt.map((bt)=>(
                        <button>{bt}</button>
                    ))}
                </li>
            </ul>
        </div>
    );
}

export default Body();