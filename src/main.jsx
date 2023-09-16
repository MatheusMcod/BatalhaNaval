import React from 'react'
import ReactDOM from 'react-dom/client'
import App from './App.jsx'
import './index.css'

//configurando as rotas
import { createBrowserRouter, RouterProvider } from 'react-router-dom'
import Home from './routes/Home.jsx'
import ChooseDifficulty from './routes/ChooseDifficulty'


const router = createBrowserRouter([
  {
    path: "/",
    element:<Home/>
  },
  {
    path: "/Difficulty",
    element: <ChooseDifficulty/> 
  
  }
])



ReactDOM.createRoot(document.getElementById('root')).render(
  <React.StrictMode>
        <App/>
        <RouterProvider router = {router} />

  </React.StrictMode>,
)
