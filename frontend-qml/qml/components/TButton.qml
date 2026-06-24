import QtQuick.Controls.Material
import QtQuick.Controls

// Standard app button with consistent styling
Button {
    property bool destructive: false

    highlighted: !destructive
    Material.accent: destructive ? Material.Red : Material.accent
}
