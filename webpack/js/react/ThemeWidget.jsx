import React from "react";

const ThemeWidget = ({ title, description }) => {
    return (
        <section className="theme-react-widget">
            <p className="theme-react-widget__eyebrow">React está ativo</p>
            <h2 className="theme-react-widget__title">{title}</h2>
            <p className="theme-react-widget__description">{description}</p>
        </section>
    );
};

export default ThemeWidget;
