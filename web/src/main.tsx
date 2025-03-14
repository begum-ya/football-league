import React from "react";
import ReactDOM from "react-dom/client";
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import "./index.css"; 
import Teams from "./pages/Teams";
import FixturesList from "./pages/FixturesList";
import Simulation from "./pages/Simulation";

ReactDOM.createRoot(document.getElementById("root")!).render(
    <React.StrictMode>
        <Router>
            <Routes>
                <Route path="/" element={<Teams />} />
                <Route path="/fixtures" element={<FixturesList />} />
                <Route path="/simulation" element={<Simulation />} />
            </Routes>
        </Router>
    </React.StrictMode>
);
