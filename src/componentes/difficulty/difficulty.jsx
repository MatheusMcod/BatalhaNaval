import style from'./difficultyStyle.module.css'
function Wdifficulty (){
    return(
        <>
        <div className={style.back}>
     
        </div>
        <div className={style.balls}>
            <div className={`${style.ball} `}><h2>FÁCIL</h2></div>
            <div className={`${style.ball} `}><h2>Medio</h2></div>
            <div className={ `${style.ball} `}><h2>Difícil</h2></div>
        </div>                 
        </>
    );
}

export default Wdifficulty;