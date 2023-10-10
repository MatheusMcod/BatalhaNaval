import './App.css'
import Home from './pages/Home'
import { BrowserRouter as Router, Routes, Route /*, Link*/ } from 'react-router-dom'


function App() {

  return (
    <div className='Conteiner'>
        <Router>
          <Routes>
            <Route path="/" exact element={<Home/>}/>
          </Routes>
        </Router>
    </div> 
  )
}

export default App
