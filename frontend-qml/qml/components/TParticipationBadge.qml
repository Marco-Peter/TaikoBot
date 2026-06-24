import QtQuick
import QtQuick.Controls.Material

// Renders a colored chip for a participation state string
Rectangle {
    property string participation: "signed_in"

    width: label.implicitWidth + 16
    height: 24
    radius: 12
    color: badgeColor()

    function badgeColor() {
        switch (participation) {
        case "signed_in":   return Material.color(Material.Green, Material.Shade200)
        case "waitlist":    return Material.color(Material.Orange, Material.Shade200)
        case "teacher":     return Material.color(Material.Blue, Material.Shade200)
        case "assistance":  return Material.color(Material.Cyan, Material.Shade200)
        case "late":        return Material.color(Material.Yellow, Material.Shade300)
        case "no_show":     return Material.color(Material.Red, Material.Shade200)
        case "signed_out":  return Material.color(Material.Grey, Material.Shade200)
        default:            return Material.color(Material.Grey, Material.Shade200)
        }
    }

    function badgeLabel() {
        switch (participation) {
        case "signed_in":   return "✓ In"
        case "waitlist":    return "⏳ Wait"
        case "teacher":     return "★ Teacher"
        case "assistance":  return "➕ Assist"
        case "late":        return "⏰ Late"
        case "no_show":     return "✗ No Show"
        case "signed_out":  return "– Out"
        default:            return participation
        }
    }

    Text {
        id: label
        anchors.centerIn: parent
        text: parent.badgeLabel()
        font.pixelSize: 11
        color: "#333"
    }
}
