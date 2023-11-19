import React from 'react'
import Styles from "../modal/modal.module.css"

export default function Modal({ isOpen, width, height, setIsModalClose }) {
    if (isOpen) {
        return (
            <div className={Styles.background}>
                <div className={Styles.container} style={{ width: width, height: height }}>
                    <div className={Styles.close}>
                        <button style={{ width: 40, height: 40, fontWeight: 900, border: "none", fontSize: 20 }} onClick={setIsModalClose}>x</button>
                    </div>

                </div>
            </div>
        );
    }
    return null;

}
